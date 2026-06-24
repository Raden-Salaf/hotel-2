<?php

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Http\Request;

class FnbOrderController extends Controller
{
    /**
     * Tampilkan semua pesanan FnB yang masuk
     * Dikelompokkan berdasarkan status: pending, preparing, delivered
     */
    public function index(Request $request)
    {
        // Ambil semua booking item beserta relasi yang dibutuhkan
        $orders = BookingItem::with(['booking', 'fnbItem.category'])
            ->when(
                $request->status,
                fn($q, $s) => $q->where('status', $s),
                // Default: tampilkan pending & preparing dulu
                fn($q) => $q->whereIn('status', ['pending', 'preparing'])
            )
            ->latest()
            ->paginate(20);

        // Statistik jumlah per status untuk header
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
     * Detail satu pesanan FnB
     */
    public function show(BookingItem $order)
    {
        // Load semua relasi yang dibutuhkan untuk tampilan detail
        $order->load(['booking.room', 'fnbItem.category']);

        return view('fnb.orders.show', compact('order'));
    }

    /**
     * Update status pesanan FnB
     * Alur: pending → preparing → delivered
     */
    public function updateStatus(Request $request, BookingItem $bookingItem)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,delivered',
        ]);

        // Validasi alur status — tidak boleh loncat atau mundur
        $allowedTransitions = [
            'pending'   => 'preparing',  // pending hanya bisa ke preparing
            'preparing' => 'delivered',  // preparing hanya bisa ke delivered
        ];

        $currentStatus = $bookingItem->status;
        $newStatus     = $request->status;

        // Cek apakah transisi status valid
        if (isset($allowedTransitions[$currentStatus]) && $allowedTransitions[$currentStatus] !== $newStatus) {
            return back()->with('error', "Status tidak bisa diubah dari {$currentStatus} ke {$newStatus}.");
        }

        $bookingItem->update(['status' => $newStatus]);

        return back()->with('success', "Status pesanan berhasil diubah ke {$newStatus}!");
    }

    /**
     * Update umum (dipanggil dari Route::resource update)
     */
    public function update(Request $request, BookingItem $order)
    {
        return $this->updateStatus($request, $order);
    }
}
