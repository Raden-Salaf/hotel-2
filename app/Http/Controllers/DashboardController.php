<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\FnbItem;
use App\Models\Payment;
use App\Models\BookingItem;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->hasRole('super_admin')) {
            $data = [
                'total_rooms'        => Room::count(),
                'available_rooms'    => Room::where('status', 'available')->count(),
                'total_bookings'     => Booking::count(),
                'pending_bookings'   => Booking::where('status', 'pending')->count(),
                'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
                'checked_in'         => Booking::where('status', 'checked_in')->count(),
                'total_revenue'      => Booking::where('status', 'checked_out')->sum('total_price'),
                // Key recent_bookings selalu ada untuk super_admin
                'recent_bookings'    => Booking::with(['room', 'user'])
                                               ->latest()
                                               ->take(5)
                                               ->get(),
            ];

        } elseif ($user->hasRole('resepsionis')) {
            $data = [
                'available_rooms'  => Room::where('status', 'available')->count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'today_checkin'    => Booking::whereDate('check_in', today())->count(),
                'today_checkout'   => Booking::whereDate('check_out', today())->count(),
                // Key recent_bookings juga ada untuk resepsionis
                'recent_bookings'  => Booking::with(['room'])
                                             ->whereIn('status', ['pending', 'confirmed'])
                                             ->latest()
                                             ->take(5)
                                             ->get(),
            ];

        } elseif ($user->hasRole('admin_fnb')) {
            $data = [
                'pending_orders'   => BookingItem::where('status', 'pending')->count(),
                'preparing_orders' => BookingItem::where('status', 'preparing')->count(),
                'delivered_today'  => BookingItem::where('status', 'delivered')
                                                 ->whereDate('updated_at', today())
                                                 ->count(),
                'recent_orders'    => BookingItem::with(['booking', 'fnbItem'])
                                                 ->where('status', 'pending')
                                                 ->latest()
                                                 ->take(5)
                                                 ->get(),
                // admin_fnb tidak punya recent_bookings, set ke collection kosong
                // agar view tidak error saat akses key ini
                'recent_bookings'  => collect(),
            ];
        }

        return view('dashboard', compact('data'));
    }
}