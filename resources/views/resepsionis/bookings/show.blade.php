@extends('layouts.app')

@section('title', 'Detail Booking — ' . $booking->booking_code)

@section('content')

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('resepsionis.bookings.index') }}" class="hover:text-green-600 transition">Booking</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">{{ $booking->booking_code }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==========================================
        KOLOM KIRI — Detail Utama
        ========================================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info booking --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">

                <div class="flex items-start justify-between mb-5 gap-3 flex-wrap">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Kode Booking</p>
                        <h2 class="text-2xl font-mono font-bold" style="color:#16a34a;">
                            {{ $booking->booking_code }}
                        </h2>
                    </div>

                    @php
                        $statusConfig = [
                            'pending' => ['bg-yellow-100 text-yellow-700', 'ti-clock'],
                            'confirmed' => ['bg-blue-100 text-blue-700', 'ti-circle-check'],
                            'checked_in' => ['bg-green-100 text-green-700', 'ti-login'],
                            'checked_out' => ['bg-gray-100 text-gray-600', 'ti-logout'],
                            'cancelled' => ['bg-red-100 text-red-600', 'ti-x'],
                        ];
                        [$statusClass, $statusIcon] = $statusConfig[$booking->status] ?? ['bg-gray-100', 'ti-help'];
                    @endphp
                    <span class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold {{ $statusClass }}">
                        <i class="ti {{ $statusIcon }} text-base"></i>
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                {{-- Grid info menginap --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i class="ti ti-calendar-down text-sm"></i> Check-in
                        </p>
                        <p class="font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i class="ti ti-calendar-up text-sm"></i> Check-out
                        </p>
                        <p class="font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i class="ti ti-building-estate text-sm"></i> Kamar
                        </p>
                        <p class="font-bold text-gray-800">{{ $booking->room->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            No. {{ $booking->room->room_number ?? '-' }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i class="ti ti-users text-sm"></i> Jumlah Tamu
                        </p>
                        <p class="font-bold text-gray-800">{{ $booking->num_guests }} orang</p>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                            <i class="ti ti-tag text-sm"></i> Tipe Booking
                        </p>
                        <p class="font-bold text-gray-800">
                            {{ $booking->booking_type === 'online' ? 'Online' : 'Walk-in' }}
                        </p>
                    </div>
                    @if($booking->createdBy)
                        <div class="p-4 rounded-xl bg-gray-50">
                            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                                <i class="ti ti-user-shield text-sm"></i> Di-input Oleh
                            </p>
                            <p class="font-bold text-gray-800">{{ $booking->createdBy->name }}</p>
                        </div>
                    @endif
                </div>

                @if($booking->special_requests)
                    <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-100">
                        <p class="text-xs font-semibold text-amber-700 mb-1">
                            <i class="ti ti-note mr-1"></i> Permintaan Khusus
                        </p>
                        <p class="text-sm text-amber-800">{{ $booking->special_requests }}</p>
                    </div>
                @endif
            </div>

            {{-- Data tamu --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-user" style="color:#16a34a;"></i>
                    Data Tamu
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nama Lengkap</p>
                        <p class="font-semibold text-gray-800">{{ $booking->guest_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. KTP / Passport</p>
                        <p class="font-semibold text-gray-800">{{ $booking->guest_id_card ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Email</p>
                        <p class="font-semibold text-gray-800 truncate">{{ $booking->guest_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. HP</p>
                        <p class="font-semibold text-gray-800">{{ $booking->guest_phone }}</p>
                    </div>
                </div>
            </div>

            {{-- ==========================================
            PESANAN F&B
            ========================================== --}}
            @if($booking->bookingItems->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="ti ti-tools-kitchen-2" style="color:#16a34a;"></i>
                            Pesanan F&B
                        </h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#f0fdf4; color:#16a34a;">
                            {{ $booking->bookingItems->count() }} item
                        </span>
                    </div>
                    <div class="space-y-3">
                        @foreach($booking->bookingItems as $item)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                                <div class="w-11 h-11 rounded-xl overflow-hidden bg-white flex-shrink-0
                                            flex items-center justify-center border border-gray-100">
                                    @if($item->fnbItem?->image)
                                        <img src="{{ Storage::url($item->fnbItem->image) }}" class="w-full h-full object-contain"
                                            alt="{{ $item->fnbItem->name }}">
                                    @else
                                        <i class="ti ti-tools-kitchen-2 text-gray-300 text-sm"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm">
                                        {{ $item->fnbItem->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->quantity }}x @
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-sm text-gray-800">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                    @php
                                        $fnbStatusConfig = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'preparing' => 'bg-blue-100 text-blue-700',
                                            'delivered' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block
                                                 {{ $fnbStatusConfig[$item->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Subtotal FnB --}}
                    <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100">
                        <span class="text-xs font-semibold text-gray-500">Subtotal F&B</span>
                        <span class="font-bold text-sm text-gray-800">
                            Rp {{ number_format($booking->bookingItems->sum('subtotal'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- ==========================================
            PESANAN LAUNDRY
            ========================================== --}}
            @if($booking->laundryOrders->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="ti ti-wash" style="color:#16a34a;"></i>
                            Pesanan Laundry
                        </h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#f0fdf4; color:#16a34a;">
                            {{ $booking->laundryOrders->count() }} item
                        </span>
                    </div>
                    <div class="space-y-3">
                        @foreach($booking->laundryOrders as $order)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                                            bg-white border border-gray-100 text-xl">
                                    {{ $order->laundryItem->icon ?? '👕' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm">
                                        {{ $order->laundryItem->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $order->quantity }}x @
                                        Rp {{ number_format($order->price, 0, ',', '.') }}
                                        <span class="text-gray-300">/ {{ $order->laundryItem->unit ?? 'pcs' }}</span>
                                    </p>
                                    @if($order->notes)
                                        <p class="text-xs text-gray-400 italic mt-0.5 flex items-center gap-1">
                                            <i class="ti ti-note text-xs"></i> {{ $order->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-sm text-gray-800">
                                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                                    </p>
                                    @php
                                        $laundryStatusConfig = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'done' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block
                                                 {{ $laundryStatusConfig[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Subtotal Laundry --}}
                    <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100">
                        <span class="text-xs font-semibold text-gray-500">Subtotal Laundry</span>
                        <span class="font-bold text-sm text-gray-800">
                            Rp {{ number_format($booking->laundryOrders->sum('subtotal'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endif

        </div>
        {{-- END KOLOM KIRI --}}

        {{-- ==========================================
        KOLOM KANAN — Aksi & Ringkasan Biaya
        ========================================== --}}
        <div class="space-y-5">

            {{-- Tombol aksi --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4">Aksi Booking</h3>
                <div class="space-y-2">

                    @if($booking->status === 'pending')
                        <form action="{{ route('resepsionis.bookings.confirm', $booking) }}" method="POST" id="act-confirm">
                            @csrf @method('PATCH')
                            <button type="button" onclick="confirmBookingAction('confirm')" class="w-full py-2.5 text-sm font-semibold text-white rounded-xl
                                               transition hover:-translate-y-0.5 flex items-center justify-center gap-2"
                                style="background:#3b82f6;">
                                <i class="ti ti-circle-check"></i>
                                Konfirmasi Booking
                            </button>
                        </form>
                    @endif

                    @if($booking->status === 'confirmed')
                        <form action="{{ route('resepsionis.bookings.check-in', $booking) }}" method="POST" id="act-checkin">
                            @csrf @method('PATCH')
                            <button type="button" onclick="confirmBookingAction('checkin')" class="w-full py-2.5 text-sm font-semibold text-white rounded-xl
                                               transition hover:-translate-y-0.5 flex items-center justify-center gap-2"
                                style="background:#16a34a;">
                                <i class="ti ti-login"></i>
                                Check-in Tamu
                            </button>
                        </form>
                    @endif

                    @if($booking->status === 'checked_in')
                        <form action="{{ route('resepsionis.bookings.check-out', $booking) }}" method="POST" id="act-checkout">
                            @csrf @method('PATCH')
                            <button type="button" onclick="confirmBookingAction('checkout')" class="w-full py-2.5 text-sm font-semibold text-white rounded-xl
                                               transition hover:-translate-y-0.5 flex items-center justify-center gap-2"
                                style="background:#d97706;">
                                <i class="ti ti-logout"></i>
                                Check-out Tamu
                            </button>
                        </form>
                    @endif

                    {{-- Shortcut order FnB / Laundry untuk tamu aktif --}}
                    @if(in_array($booking->status, ['confirmed', 'checked_in']))
                        <a href="{{ route('fnb.orders.create') }}" class="w-full py-2.5 text-sm font-semibold rounded-xl
                                      transition hover:-translate-y-0.5 flex items-center justify-center gap-2"
                            style="background:#fff7ed; color:#c2410c;">
                            <i class="ti ti-tools-kitchen-2"></i>
                            Tambah Pesanan F&B
                        </a>
                        <a href="{{ route('laundry.orders.create') }}" class="w-full py-2.5 text-sm font-semibold rounded-xl
                                      transition hover:-translate-y-0.5 flex items-center justify-center gap-2"
                            style="background:#eff6ff; color:#1d4ed8;">
                            <i class="ti ti-wash"></i>
                            Tambah Pesanan Laundry
                        </a>
                    @endif

                    @if(!in_array($booking->status, ['pending', 'cancelled']))
                        <a href="{{ route('resepsionis.bookings.invoice', $booking) }}" class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-semibold
                                      rounded-xl transition hover:-translate-y-0.5" style="background:#f5f3ff; color:#7c3aed;">
                            <i class="ti ti-receipt"></i>
                            Lihat & Cetak Invoice
                        </a>
                    @endif

                    @if(!in_array($booking->status, ['checked_in', 'checked_out', 'cancelled']))
                        <form action="{{ route('resepsionis.bookings.destroy', $booking) }}" method="POST" id="act-cancel">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmBookingAction('cancel')" class="w-full py-2.5 text-sm font-semibold text-red-600 bg-red-50
                                               rounded-xl hover:bg-red-100 transition flex items-center justify-center gap-2">
                                <i class="ti ti-x"></i>
                                Batalkan Booking
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('resepsionis.bookings.index') }}" class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-medium
                              text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                        <i class="ti ti-arrow-left text-sm"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Ringkasan biaya --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4">Ringkasan Biaya</h3>
                <div class="space-y-2 text-sm">

                    <div class="flex justify-between text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-building-estate text-sm"></i> Harga Kamar
                        </span>
                        <span>Rp {{ number_format($booking->room_price, 0, ',', '.') }}</span>
                    </div>

                    @if($booking->bookingItems->count() > 0)
                        <div class="flex justify-between text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <i class="ti ti-tools-kitchen-2 text-sm"></i> Total F&B
                            </span>
                            <span>Rp {{ number_format($booking->bookingItems->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($booking->laundryOrders->count() > 0)
                        <div class="flex justify-between text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <i class="ti ti-wash text-sm"></i> Total Laundry
                            </span>
                            <span>Rp {{ number_format($booking->laundryOrders->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($booking->invoice)
                        <div class="flex justify-between text-gray-500">
                            <span>Pajak (11%)</span>
                            <span>Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-bold text-gray-800 pt-2 border-t border-gray-100">
                        <span>Grand Total</span>
                        <span style="color:#16a34a;">
                            Rp {{ number_format($booking->invoice->total ?? $booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($booking->invoice)
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-xs text-gray-400">Status Pembayaran</span>
                            @if($booking->invoice->status === 'paid')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                     bg-green-100 text-green-700 flex items-center gap-1">
                                    <i class="ti ti-circle-check text-xs"></i> Lunas
                                </span>
                            @elseif($booking->invoice->status === 'cancelled')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                     bg-red-100 text-red-600">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                     bg-yellow-100 text-yellow-700">
                                    Belum Lunas
                                </span>
                            @endif
                        </div>

                        @if($booking->invoice->status === 'paid' && $booking->invoice->paid_at)
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-400">Dibayar Pada</span>
                                <span class="text-xs font-medium text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->invoice->paid_at)->format('d M Y') }}
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
        {{-- END KOLOM KANAN --}}

    </div>

@endsection

@push('scripts')
    <script>
        function confirmBookingAction(type) {
            const configs = {
                confirm: {
                    title: 'Konfirmasi Booking?', text: 'Booking akan diubah ke status Confirmed.',
                    icon: 'question', confirmText: 'Ya, Konfirmasi!', color: '#16a34a', formId: 'act-confirm'
                },
                checkin: {
                    title: 'Check-in Tamu?', text: 'Tamu akan di-check-in dan kamar berubah ke Occupied.',
                    icon: 'info', confirmText: 'Check-in!', color: '#16a34a', formId: 'act-checkin'
                },
                checkout: {
                    title: 'Check-out Tamu?', text: 'Tamu akan di-check-out dan kamar kembali Available.',
                    icon: 'warning', confirmText: 'Check-out!', color: '#d97706', formId: 'act-checkout'
                },
                cancel: {
                    title: 'Batalkan Booking?', text: 'Booking akan dibatalkan dan tidak bisa dikembalikan!',
                    icon: 'error', confirmText: 'Ya, Batalkan!', color: '#dc2626', formId: 'act-cancel'
                },
            }
            const cfg = configs[type]
            if (!cfg) return
            Swal.fire({
                title: cfg.title, text: cfg.text, icon: cfg.icon,
                showCancelButton: true, confirmButtonColor: cfg.color, cancelButtonColor: '#6b7280',
                confirmButtonText: cfg.confirmText, cancelButtonText: 'Batal', reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) document.getElementById(cfg.formId).submit()
            })
        }
    </script>
@endpush
