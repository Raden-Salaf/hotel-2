<?php

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\FnbCategory;
use App\Models\FnbItem;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FnbOrderController extends Controller
{
    /**
     * Tampilkan semua pesanan FnB yang masuk
     */
    public function index(Request $request)
    {
        $orders = BookingItem::with(['booking.room', 'fnbItem.category'])
            ->when(
                $request->status,
                fn($q, $s) => $q->where('status', $s),
                fn($q)     => $q->whereIn('status', ['pending', 'preparing'])
            )
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'   => BookingItem::where('status', 'pending')->count(),
            'preparing' => BookingItem::where('status', 'preparing')->count(),
            'delivered' => BookingItem::whereDate('updated_at', today())
                ->where('status', 'delivered')
                ->count(),
        ];

        return view('fnb.orders.index', compact('orders', 'stats'));
    }

    /**
     * Form buat order FnB baru untuk tamu yang sudah confirmed/checked_in
     */
    public function create()
    {
        // Hanya booking yang confirmed atau checked_in yang bisa order FnB
        $bookings = Booking::with(['room', 'bookingItems'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest()
            ->get();

        // Ambil semua menu FnB yang tersedia
        $fnbItems      = FnbItem::with('category')
            ->where('is_available', true)
            ->get()
            ->groupBy('fnb_category_id');

        $fnbCategories = FnbCategory::all()->keyBy('id');

        return view('fnb.orders.create', compact('bookings', 'fnbItems', 'fnbCategories'));
    }

    /**
     * Simpan order FnB baru
     * Ditambahkan ke booking yang sudah ada
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'fnb'        => 'required|array|min:1',
            'fnb.*'      => 'integer|min:1',
        ]);

        // Pastikan booking memang confirmed atau checked_in
        $booking = Booking::with('invoice')->findOrFail($request->booking_id);

        if (!in_array($booking->status, ['confirmed', 'checked_in'])) {
            return back()->with('error', 'Booking ini tidak bisa ditambahkan pesanan FnB.');
        }

        $fnbOrders    = [];
        $totalTambahan = 0;

        foreach ($request->fnb as $fnbItemId => $quantity) {
            if ($quantity < 1) continue;

            $fnbItem = FnbItem::find($fnbItemId);
            if (!$fnbItem || !$fnbItem->is_available) continue;

            $subtotal       = $fnbItem->price * $quantity;
            $totalTambahan += $subtotal;

            $fnbOrders[] = [
                'fnb_item_id' => $fnbItemId,
                'quantity'    => $quantity,
                'price'       => $fnbItem->price,
                'subtotal'    => $subtotal,
            ];
        }

        if (empty($fnbOrders)) {
            return back()->with('error', 'Pilih minimal satu menu F&B.');
        }

        DB::transaction(function () use ($booking, $fnbOrders, $totalTambahan) {

            // Tambahkan booking items baru
            foreach ($fnbOrders as $order) {
                $booking->bookingItems()->create($order);
            }

            // Update total fnb_price dan total_price di booking
            $booking->increment('fnb_price', $totalTambahan);
            $booking->increment('total_price', $totalTambahan);

            // Update invoice jika ada
            if ($booking->invoice) {
                $tambahPajak = $totalTambahan * 0.11;

                $booking->invoice->increment('subtotal', $totalTambahan);
                $booking->invoice->increment('tax', $tambahPajak);
                $booking->invoice->increment('total', $totalTambahan + $tambahPajak);

                // Jika invoice sudah paid, tetap paid
                // Jika belum, tambahkan tagihan ke invoice
            }
        });

        return redirect()
            ->route('fnb.orders.index')
            ->with('success', 'Pesanan FnB berhasil ditambahkan untuk tamu ' . $booking->guest_name . '!');
    }

    /**
     * Detail pesanan FnB
     */
    public function show(BookingItem $order)
    {
        $order->load(['booking.room', 'fnbItem.category']);

        return view('fnb.orders.show', compact('order'));
    }

    /**
     * Update status pesanan
     * Alur: pending → preparing → delivered
     */
    public function updateStatus(Request $request, BookingItem $bookingItem)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,delivered',
        ]);

        $allowedTransitions = [
            'pending'   => 'preparing',
            'preparing' => 'delivered',
        ];

        $currentStatus = $bookingItem->status;
        $newStatus     = $request->status;

        if (
            isset($allowedTransitions[$currentStatus]) &&
            $allowedTransitions[$currentStatus] !== $newStatus
        ) {
            return back()->with('error', "Status tidak bisa diubah dari {$currentStatus} ke {$newStatus}.");
        }

        $bookingItem->update(['status' => $newStatus]);

        return back()->with('success', "Status pesanan berhasil diubah ke {$newStatus}!");
    }

    /**
     * Update umum dari resource
     */
    public function update(Request $request, BookingItem $order)
    {
        return $this->updateStatus($request, $order);
    }
}
