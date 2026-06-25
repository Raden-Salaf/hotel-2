<?php

namespace App\Http\Controllers\Public;

use App\Helpers\BookingHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FnbItem;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\FnbCategory;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicBookingController extends Controller
{
    /**
     * Halaman utama — tampilkan semua kamar tersedia
     */
    public function index(Request $request)
    {
        $categories = \App\Models\RoomCategory::withCount('rooms')->get();

        $rooms = Room::with('category')
            ->where('status', 'available')
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
        if ($room->status !== 'available') {
            return redirect()
                ->route('public.booking.index')
                ->with('error', 'Kamar ini sedang tidak tersedia.');
        }

        $fnbItems      = FnbItem::with('category')
            ->where('is_available', true)
            ->get()
            ->groupBy('fnb_category_id');

        $fnbCategories = FnbCategory::all()->keyBy('id');

        return view('public.booking.show', compact('room', 'fnbItems', 'fnbCategories'));
    }

    /**
     * Proses form booking dari tamu online
     */
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'guest_name'       => 'required|string|max:100',
            'guest_email'      => 'required|email',
            'guest_phone'      => 'required|string|max:20',
            'guest_id_card'    => 'nullable|string|max:30',
            'check_in'         => 'required|date|after_or_equal:today',
            'check_out'        => 'required|date|after:check_in',
            'num_guests'       => 'required|integer|min:1|max:' . $room->capacity,
            'special_requests' => 'nullable|string|max:500',
            'fnb'              => 'nullable|array',
            'fnb.*'            => 'integer|min:1',
        ]);

        $nights    = BookingHelper::calculateNights($validated['check_in'], $validated['check_out']);
        $roomPrice = BookingHelper::calculateRoomPrice($room->price_per_night, $nights);
        $fnbPrice  = 0;
        $fnbOrders = [];

        if (!empty($validated['fnb'])) {
            foreach ($validated['fnb'] as $fnbItemId => $quantity) {
                if ($quantity < 1) continue;
                $fnbItem    = FnbItem::find($fnbItemId);
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

        // Gunakan DB transaction agar semua tersimpan atau semua rollback
        $booking = DB::transaction(function () use ($validated, $room, $roomPrice, $fnbPrice, $totalPrice, $fnbOrders) {

            $booking = Booking::create([
                'booking_code'     => BookingHelper::generateBookingCode(),
                'user_id'          => auth()->id(),
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

            foreach ($fnbOrders as $order) {
                $booking->bookingItems()->create($order);
            }

            // Buat invoice — status unpaid sampai Midtrans konfirmasi
            $tax = $totalPrice * 0.11;
            Invoice::create([
                'invoice_number' => BookingHelper::generateInvoiceNumber(),
                'booking_id'     => $booking->id,
                'subtotal'       => $totalPrice,
                'tax'            => $tax,
                'discount'       => 0,
                'total'          => $totalPrice + $tax,
                'status'         => 'unpaid',
                'due_date'       => now()->addDays(1),
            ]);

            return $booking;
        });

        return redirect()->route('public.booking.confirmation', $booking->id);
    }

    /**
     * Halaman konfirmasi booking — tampilkan detail + tombol bayar Midtrans
     */
    public function confirmation(Booking $booking)
    {
        $booking->load(['room', 'bookingItems.fnbItem', 'invoice']);

        $snapToken = null;
        $snapError = null;

        if ($booking->booking_type === 'online' && $booking->invoice?->status === 'unpaid') {
            try {
                $midtrans  = new MidtransService();
                $snapToken = $midtrans->createSnapToken($booking);
            } catch (\Exception $e) {
                // Sementara simpan error untuk ditampilkan di view
                $snapError = $e->getMessage();
                Log::error('Midtrans snap token error: ' . $e->getMessage());
            }
        }

        return view('public.booking.confirmation', compact('booking', 'snapToken', 'snapError'));
    }

    /**
     * Callback dari Midtrans (webhook/notification)
     * Dipanggil otomatis oleh server Midtrans setiap ada update status bayar
     * Route ini tidak pakai CSRF (sudah dikecualikan di web.php)
     */
    public function paymentCallback(Request $request)
    {
        try {
            $midtrans = new MidtransService();
            $data     = $midtrans->handleNotification();

            // Cari payment record berdasarkan order_id dari Midtrans
            $payment  = Payment::where('order_id', $data['order_id'])->firstOrFail();
            $booking  = $payment->booking;
            $invoice  = $payment->invoice;

            // Update payment record
            $payment->update([
                'status'            => $data['payment_status'],
                'payment_type'      => $data['payment_type'],
                'transaction_id'    => $data['transaction_id'],
                'midtrans_response' => $request->all(),
                'paid_at'           => $data['payment_status'] === 'success' ? now() : null,
            ]);

            // Jika pembayaran berhasil, update invoice dan booking
            if ($data['payment_status'] === 'success') {
                DB::transaction(function () use ($invoice, $booking) {
                    // Tandai invoice lunas
                    $invoice->update([
                        'status'  => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Konfirmasi booking otomatis
                    $booking->update(['status' => 'confirmed']);
                });
            }

            // Jika gagal/expired, update status payment saja
            // Booking tetap pending sampai tamu bayar ulang
            if ($data['payment_status'] === 'failed') {
                $payment->update(['status' => 'failed']);
            }

            // Return 200 ke Midtrans — penting! Jika tidak 200,
            // Midtrans akan retry callback sampai 5 kali
            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Halaman setelah tamu selesai bayar di Midtrans
     * Midtrans redirect tamu ke sini setelah proses payment
     */
    public function paymentFinish(Booking $booking)
    {
        // Reload data terbaru dari DB
        $booking->load(['room', 'bookingItems.fnbItem', 'invoice']);

        return view('public.booking.payment-finish', compact('booking'));
    }
}
