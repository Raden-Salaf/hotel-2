<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LaundryItem;
use App\Models\LaundryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaundryOrderController extends Controller
{
    /**
     * Tampilkan semua pesanan laundry
     */
    public function index(Request $request)
    {
        $orders = LaundryOrder::with(['booking.room', 'laundryItem', 'createdBy'])
            ->when(
                $request->status,
                fn($q, $s) => $q->where('status', $s),
                // Default: tampilkan pending & processing
                fn($q)     => $q->whereIn('status', ['pending', 'processing'])
            )
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'    => LaundryOrder::where('status', 'pending')->count(),
            'processing' => LaundryOrder::where('status', 'processing')->count(),
            'done'       => LaundryOrder::where('status', 'done')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return view('laundry.orders.index', compact('orders', 'stats'));
    }

    /**
     * Form buat pesanan laundry baru
     */
    public function create()
    {
        // Hanya booking confirmed/checked_in
        $bookings = Booking::with(['room', 'laundryOrders'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest()
            ->get();

        // Item laundry yang tersedia
        $laundryItems = LaundryItem::where('is_available', true)
            ->latest()
            ->get();

        return view('laundry.orders.create', compact('bookings', 'laundryItems'));
    }

    /**
     * Simpan pesanan laundry baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'items'      => 'required|array|min:1',
            'items.*.laundry_item_id' => 'required|exists:laundry_items,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.notes'           => 'nullable|string|max:200',
        ]);

        $booking = Booking::with('invoice')->findOrFail($request->booking_id);

        if (!in_array($booking->status, ['confirmed', 'checked_in'])) {
            return back()->with('error', 'Booking ini tidak bisa ditambahkan pesanan laundry.');
        }

        $totalTambahan = 0;
        $orders        = [];

        foreach ($request->items as $item) {
            if (empty($item['laundry_item_id']) || empty($item['quantity'])) continue;
            if ((int) $item['quantity'] < 1) continue;

            $laundryItem = LaundryItem::find($item['laundry_item_id']);
            if (!$laundryItem || !$laundryItem->is_available) continue;

            $subtotal       = $laundryItem->price * $item['quantity'];
            $totalTambahan += $subtotal;

            $orders[] = [
                'booking_id'      => $booking->id,
                'laundry_item_id' => $laundryItem->id,
                'quantity'        => $item['quantity'],
                'price'           => $laundryItem->price,
                'subtotal'        => $subtotal,
                'notes'           => $item['notes'] ?? null,
                'status'          => 'pending',
                'created_by'      => auth()->id(),
            ];
        }

        if (empty($orders)) {
            return back()->with('error', 'Pilih minimal satu item laundry dengan jumlah yang valid.');
        }

        DB::transaction(function () use ($booking, $orders, $totalTambahan) {

            // Simpan semua pesanan laundry
            foreach ($orders as $order) {
                LaundryOrder::create($order);
            }

            // Update total di booking
            $booking->increment('total_price', $totalTambahan);

            // Update invoice jika ada
            if ($booking->invoice) {
                $tambahPajak = $totalTambahan * 0.11;
                $booking->invoice->increment('subtotal', $totalTambahan);
                $booking->invoice->increment('tax', $tambahPajak);
                $booking->invoice->increment('total', $totalTambahan + $tambahPajak);
            }
        });

        return redirect()
            ->route('laundry.orders.index')
            ->with('success', "Pesanan laundry berhasil dibuat untuk tamu {$booking->guest_name}!");
    }

    /**
     * Update status pesanan laundry
     * Alur: pending → processing → done
     */
    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done',
        ]);

        $allowedTransitions = [
            'pending'    => 'processing',
            'processing' => 'done',
        ];

        $currentStatus = $order->status;
        $newStatus     = $request->status;

        if (
            isset($allowedTransitions[$currentStatus]) &&
            $allowedTransitions[$currentStatus] !== $newStatus
        ) {
            return back()->with(
                'error',
                "Status tidak bisa diubah dari {$currentStatus} ke {$newStatus}."
            );
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', "Status laundry berhasil diubah ke {$newStatus}!");
    }
}
