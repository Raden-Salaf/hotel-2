<?php

namespace App\Http\Controllers\Resepsionis;

use App\Helpers\BookingHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FnbItem;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\FnbCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Daftar semua booking
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['room', 'invoice'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $s) {
                $q->where('booking_code', 'like', "%{$s}%")
                    ->orWhere('guest_name', 'like', "%{$s}%");
            })
            ->when($request->type, fn($q, $t) => $q->where('booking_type', $t))
            ->latest()
            ->paginate(15);

        $stats = [
            'pending'    => Booking::where('status', 'pending')->count(),
            'confirmed'  => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'today_in'   => Booking::whereDate('check_in', today())->count(),
            'today_out'  => Booking::whereDate('check_out', today())->count(),
        ];

        return view('resepsionis.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Form booking walk-in
     */
    public function create()
    {
        $rooms         = Room::with('category')->where('status', 'available')->get();
        $fnbItems      = FnbItem::with('category')->where('is_available', true)->get()->groupBy('fnb_category_id');
        $fnbCategories = FnbCategory::all()->keyBy('id');

        return view('resepsionis.bookings.create', compact('rooms', 'fnbItems', 'fnbCategories'));
    }

    /**
     * Simpan booking walk-in
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'guest_name'       => 'required|string|max:100',
            'guest_email'      => 'required|email',
            'guest_phone'      => 'required|string|max:20',
            'guest_id_card'    => 'nullable|string|max:30',
            'check_in'         => 'required|date|after_or_equal:today', // ← pastikan ada
            'check_out'        => 'required|date|after:check_in',       // ← pastikan ada
            'num_guests'       => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'fnb'              => 'nullable|array',
            'fnb.*'            => 'integer|min:1',
        ]);

        $room      = Room::findOrFail($validated['room_id']);
        $nights    = BookingHelper::calculateNights($validated['check_in'], $validated['check_out']);
        $roomPrice = BookingHelper::calculateRoomPrice($room->price_per_night, $nights);
        $fnbPrice  = 0;
        $fnbOrders = [];

        if (!empty($validated['fnb'])) {
            foreach ($validated['fnb'] as $fnbItemId => $quantity) {
                if ($quantity < 1) continue;
                $fnbItem    = FnbItem::find($fnbItemId);
                if (!$fnbItem) continue;
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

        DB::transaction(function () use ($validated, $room, $roomPrice, $fnbPrice, $totalPrice, $fnbOrders) {
            $booking = Booking::create([
                'booking_code'     => BookingHelper::generateBookingCode(),
                'room_id'          => $room->id,
                'guest_name'       => $validated['guest_name'],
                'guest_email'      => $validated['guest_email'],
                'guest_phone'      => $validated['guest_phone'],
                'guest_id_card'    => $validated['guest_id_card'] ?? null,
                'check_in'         => $validated['check_in'],
                'check_out'        => $validated['check_out'],
                'num_guests'       => $validated['num_guests'],
                'special_requests' => $validated['special_requests'] ?? null,
                'booking_type'     => 'walk_in',
                'status'           => 'confirmed',
                'room_price'       => $roomPrice,
                'fnb_price'        => $fnbPrice,
                'total_price'      => $totalPrice,
                'created_by'       => auth()->id(),
            ]);

            foreach ($fnbOrders as $order) {
                $booking->bookingItems()->create($order);
            }

            $tax = $totalPrice * 0.11;

            // Walk-in langsung buat invoice PAID karena bayar di tempat
            Invoice::create([
                'invoice_number' => BookingHelper::generateInvoiceNumber(),
                'booking_id'     => $booking->id,
                'subtotal'       => $totalPrice,
                'tax'            => $tax,
                'discount'       => 0,
                'total'          => $totalPrice + $tax,
                'status'         => 'paid',    // ← langsung paid untuk walk-in
                'paid_at'        => now(),     // ← catat waktu bayar
                'due_date'       => now(),
            ]);
        });

        return redirect()
            ->route('resepsionis.bookings.index')
            ->with('success', 'Booking walk-in berhasil dibuat & invoice otomatis lunas!');
    }

    /**
     * Detail booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['room.category', 'bookingItems.fnbItem', 'invoice', 'createdBy']);
        $room = Room::find($booking->room_id);
        // return $booking;
        return view('resepsionis.bookings.show', compact('booking', 'room'));
    }


    /**
     * Konfirmasi booking online (pending → confirmed)
     * Walk-in tidak akan masuk sini karena langsung confirmed saat dibuat
     * Method ini khusus untuk booking ONLINE yang pending
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini tidak bisa dikonfirmasi.');
        }

        DB::transaction(function () use ($booking) {

            $booking->update(['status' => 'confirmed']);

            // Jika ternyata ada walk-in yang masih pending (edge case),
            // sekalian tandai invoicenya lunas
            if ($booking->booking_type === 'walk_in') {
                $invoice = $booking->invoice()->first();
                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->update([
                        'status'  => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }
        });

        $message = $booking->booking_type === 'walk_in'
            ? "Booking {$booking->booking_code} dikonfirmasi & invoice lunas!"
            : "Booking {$booking->booking_code} berhasil dikonfirmasi!";

        return back()->with('success', $message);
    }

    /**
     * Check-in tamu (confirmed → checked_in)
     * Sekaligus pastikan invoice walk-in sudah paid
     */
    public function checkIn(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Tamu belum bisa check-in.');
        }

        DB::transaction(function () use ($booking) {

            // Update status booking
            $booking->update(['status' => 'checked_in']);

            // Update kamar jadi occupied
            $booking->room->update(['status' => 'occupied']);

            // Pastikan invoice walk-in lunas saat check-in
            // Ini safety net jika entah bagaimana invoice masih unpaid
            if ($booking->booking_type === 'walk_in') {
                $invoice = $booking->invoice()->first();
                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->update([
                        'status'  => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', "Tamu {$booking->guest_name} berhasil check-in!");
    }

    /**
     * Check-out tamu (checked_in → checked_out)
     * Sekaligus pastikan invoice sudah paid sebelum check-out
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Tamu belum bisa check-out.');
        }

        // Cek invoice — untuk walk-in harus sudah lunas sebelum check-out
        $invoice = $booking->invoice()->first();

        if ($booking->booking_type === 'walk_in' && $invoice && $invoice->status !== 'paid') {
            // Paksa lunas saat check-out jika belum
            $invoice->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);
        }

        DB::transaction(function () use ($booking) {
            // Update status booking
            $booking->update(['status' => 'checked_out']);

            // Kembalikan kamar jadi available
            $booking->room->update(['status' => 'available']);
        });

        return back()->with('success', "Tamu {$booking->guest_name} berhasil check-out!");
    }

    /**
     * Tampilkan invoice
     */
    public function invoice(Booking $booking)
    {
        $booking->load(['room', 'bookingItems.fnbItem.category', 'invoice']);

        return view('resepsionis.bookings.invoice', compact('booking'));
    }

    /**
     * Edit booking
     */
    public function edit(Booking $booking)
    {
        $rooms = Room::with('category')->where('status', 'available')->get();

        return view('resepsionis.bookings.edit', compact('booking', 'rooms'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'guest_name'       => 'required|string|max:100',
            'guest_email'      => 'required|email',
            'guest_phone'      => 'required|string|max:20',
            'special_requests' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()
            ->route('resepsionis.bookings.show', $booking)
            ->with('success', 'Data booking berhasil diperbarui!');
    }

    /**
     * Batalkan booking
     */
    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'checked_out'])) {
            return back()->with('error', 'Booking yang sudah check-in tidak bisa dibatalkan.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->invoice) {
                $booking->invoice->update(['status' => 'cancelled']);
            }
        });

        return redirect()
            ->route('resepsionis.bookings.index')
            ->with('success', "Booking {$booking->booking_code} berhasil dibatalkan.");
    }
}
