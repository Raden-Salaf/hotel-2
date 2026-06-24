<?php

namespace App\Http\Controllers\Public;

use App\Helpers\BookingHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FnbItem;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    /**
     * Halaman utama — tampilkan semua kamar yang tersedia
     */
    public function index(Request $request)
    {
        // Ambil semua kategori untuk filter
        $categories = RoomCategory::withCount('rooms')->get();

        // Query kamar dengan filter
        $rooms = Room::with('category')
                     ->where('status', 'available') // hanya kamar tersedia
                     ->when($request->category, fn($q, $c) => $q->where('room_category_id', $c))
                     ->when($request->min_price, fn($q, $p) => $q->where('price_per_night', '>=', $p))
                     ->when($request->max_price, fn($q, $p) => $q->where('price_per_night', '<=', $p))
                     ->when($request->capacity, fn($q, $c) => $q->where('capacity', '>=', $c))
                     ->paginate(9);

        return view('public.booking.index', compact('rooms', 'categories'));
    }

    /**
     * Detail kamar + form booking
     */
    public function show(Room $room, Request $request)
    {
        // Kamar yang sedang maintenance atau occupied tidak bisa dipesan
        if ($room->status !== 'available') {
            return redirect()
                   ->route('public.booking.index')
                   ->with('error', 'Kamar ini sedang tidak tersedia.');
        }

        // Ambil menu FnB yang tersedia untuk ditambah ke booking
        $fnbItems = FnbItem::with('category')
                           ->where('is_available', true)
                           ->get()
                           ->groupBy('fnb_category_id'); // group by kategori

        $fnbCategories = \App\Models\FnbCategory::all()->keyBy('id');

        return view('public.booking.show', compact('room', 'fnbItems', 'fnbCategories'));
    }

    /**
     * Proses form booking dari tamu
     */
    public function store(Request $request, Room $room)
    {
        // Validasi semua input dari form
        $validated = $request->validate([
            'guest_name'      => 'required|string|max:100',
            'guest_email'     => 'required|email',
            'guest_phone'     => 'required|string|max:20',
            'guest_id_card'   => 'nullable|string|max:30',
            'check_in'        => 'required|date|after_or_equal:today',
            'check_out'       => 'required|date|after:check_in',
            'num_guests'      => 'required|integer|min:1|max:' . $room->capacity,
            'special_requests'=> 'nullable|string|max:500',
            // FnB — optional, berupa array [fnb_item_id => quantity]
            'fnb'             => 'nullable|array',
            'fnb.*'           => 'integer|min:1',
        ]);

        // Hitung jumlah malam
        $nights = BookingHelper::calculateNights($validated['check_in'], $validated['check_out']);

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimal menginap 1 malam.']);
        }

        // Hitung harga kamar
        $roomPrice = BookingHelper::calculateRoomPrice($room->price_per_night, $nights);

        // Hitung harga FnB jika ada
        $fnbPrice = 0;
        $fnbOrders = [];

        if (!empty($validated['fnb'])) {
            foreach ($validated['fnb'] as $fnbItemId => $quantity) {
                if ($quantity < 1) continue;

                $fnbItem = FnbItem::find($fnbItemId);
                if (!$fnbItem || !$fnbItem->is_available) continue;

                $subtotal   = $fnbItem->price * $quantity;
                $fnbPrice  += $subtotal;
                $fnbOrders[] = [
                    'fnb_item_id' => $fnbItemId,
                    'quantity'    => $quantity,
                    'price'       => $fnbItem->price,
                    'subtotal'    => $subtotal,
                ];
            }
        }

        $totalPrice = $roomPrice + $fnbPrice;

        // Gunakan DB transaction agar semua tersimpan atau semua gagal
        // Kalau di tengah jalan error, semua rollback otomatis
        DB::transaction(function () use ($validated, $room, $roomPrice, $fnbPrice, $totalPrice, $fnbOrders) {

            // 1. Buat record booking
            $booking = Booking::create([
                'booking_code'     => BookingHelper::generateBookingCode(),
                'user_id'          => auth()->id(), // null jika tidak login
                'room_id'          => $room->id,
                'guest_name'       => $validated['guest_name'],
                'guest_email'      => $validated['guest_email'],
                'guest_phone'      => $validated['guest_phone'],
                'guest_id_card'    => $validated['guest_id_card'] ?? null,
                'check_in'         => $validated['check_in'],
                'check_out'        => $validated['check_out'],
                'num_guests'       => $validated['num_guests'],
                'special_requests' => $validated['special_requests'] ?? null,
                'booking_type'     => 'online',
                'status'           => 'pending',
                'room_price'       => $roomPrice,
                'fnb_price'        => $fnbPrice,
                'total_price'      => $totalPrice,
            ]);

            // 2. Simpan pesanan FnB jika ada
            foreach ($fnbOrders as $order) {
                $booking->bookingItems()->create($order);
            }

            // 3. Buat invoice otomatis
            // Pajak 11% dari subtotal
            $tax      = $totalPrice * 0.11;
            $grandTotal = $totalPrice + $tax;

            Invoice::create([
                'invoice_number' => BookingHelper::generateInvoiceNumber(),
                'booking_id'     => $booking->id,
                'subtotal'       => $totalPrice,
                'tax'            => $tax,
                'discount'       => 0,
                'total'          => $grandTotal,
                'status'         => 'unpaid',
                'due_date'       => now()->addDays(1), // batas bayar 24 jam
            ]);

            // Simpan booking_code ke session untuk halaman konfirmasi
            session(['last_booking_code' => $booking->booking_code]);
            session(['last_booking_id'   => $booking->id]);
        });

        return redirect()->route('public.booking.confirmation', [
            'booking' => session('last_booking_id')
        ]);
    }

    /**
     * Halaman konfirmasi setelah booking berhasil
     */
    public function confirmation(Booking $booking)
    {
        // Load semua relasi yang dibutuhkan untuk tampilan
        $booking->load(['room', 'bookingItems.fnbItem', 'invoice']);

        return view('public.booking.confirmation', compact('booking'));
    }
}