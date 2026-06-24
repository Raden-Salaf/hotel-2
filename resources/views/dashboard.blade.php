@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- Greeting --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Selamat datang, {{ auth()->user()->name }}! 👋
        </h2>
        <p class="text-gray-500 mt-1 text-sm">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- ==========================================
     SUPER ADMIN DASHBOARD
========================================== --}}
    @role('super_admin')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Total Kamar</span>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-building-estate text-blue-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['total_rooms'] }}</p>
                <p class="text-sm text-green-500 mt-1">{{ $data['available_rooms'] }} tersedia</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Total Booking</span>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-calendar text-amber-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['total_bookings'] }}</p>
                <p class="text-sm text-amber-500 mt-1">{{ $data['pending_bookings'] }} pending</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Sedang Menginap</span>
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-users text-green-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['checked_in'] }}</p>
                <p class="text-sm text-gray-400 mt-1">tamu aktif</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Total Revenue</span>
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-currency-dollar text-purple-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-400 mt-1">dari checkout selesai</p>
            </div>
        </div>
    @endrole

    {{-- ==========================================
     RESEPSIONIS DASHBOARD
========================================== --}}
    @role('resepsionis')
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Kamar Tersedia</span>
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-building-estate text-green-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['available_rooms'] }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Booking Pending</span>
                    <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-clock text-yellow-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['pending_bookings'] }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Check-in Hari Ini</span>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-login text-blue-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['today_checkin'] }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Check-out Hari Ini</span>
                    <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-logout text-orange-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['today_checkout'] }}</p>
            </div>
        </div>
    @endrole

    {{-- ==========================================
     ADMIN FNB DASHBOARD
========================================== --}}
    @role('admin_fnb')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Pesanan Pending</span>
                    <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-clock text-yellow-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['pending_orders'] }}</p>
                <p class="text-sm text-yellow-500 mt-1">menunggu diproses</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Sedang Diproses</span>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-tools-kitchen-2 text-blue-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['preparing_orders'] }}</p>
                <p class="text-sm text-blue-500 mt-1">sedang dimasak</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Terkirim Hari Ini</span>
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="ti ti-circle-check text-green-500 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $data['delivered_today'] }}</p>
                <p class="text-sm text-green-500 mt-1">selesai hari ini</p>
            </div>
        </div>

        {{-- Pesanan FnB terbaru untuk admin FnB --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Pesanan Masuk Terbaru</h3>
                <a href="{{ route('fnb.orders.index') }}" class="text-xs font-medium hover:underline" style="color:#16a34a;">
                    Lihat semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase">
                            <th class="text-left px-6 py-3">Menu</th>
                            <th class="text-left px-6 py-3">Tamu</th>
                            <th class="text-left px-6 py-3">Qty</th>
                            <th class="text-left px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data['recent_orders'] as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    {{ $order->fnbItem->name ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ $order->booking->guest_name ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $order->quantity }}x
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                                    Belum ada pesanan masuk
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endrole

    {{-- ==========================================
     TABEL BOOKING TERBARU
     Tampil untuk super_admin dan resepsionis
========================================== --}}
    @role('super_admin|resepsionis')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mt-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Booking Terbaru</h3>
                <a href="{{ route('resepsionis.bookings.index') }}" class="text-xs font-medium hover:underline"
                    style="color:#16a34a;">
                    Lihat semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase">
                            <th class="text-left px-6 py-3">Kode Booking</th>
                            <th class="text-left px-6 py-3">Tamu</th>
                            <th class="text-left px-6 py-3">Kamar</th>
                            <th class="text-left px-6 py-3">Check-in</th>
                            <th class="text-left px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data['recent_bookings'] as $booking)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-mono font-bold text-sm" style="color:#16a34a;">
                                    {{ $booking->booking_code }}
                                </td>
                                <td class="px-6 py-3 text-gray-700">{{ $booking->guest_name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $booking->room->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColor = match ($booking->status) {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'confirmed' => 'bg-blue-100 text-blue-700',
                                            'checked_in' => 'bg-green-100 text-green-700',
                                            'checked_out' => 'bg-gray-100 text-gray-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                                    Belum ada booking
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endrole

@endsection
