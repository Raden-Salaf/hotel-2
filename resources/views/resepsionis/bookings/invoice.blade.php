@extends('layouts.app')

@section('title', 'Invoice — ' . $booking->booking_code)

@section('content')

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('resepsionis.bookings.index') }}"
           class="hover:text-green-600 transition">Booking</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <a href="{{ route('resepsionis.bookings.show', $booking) }}"
           class="hover:text-green-600 transition">
            {{ $booking->booking_code }}
        </a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Invoice</span>
    </div>

    {{-- Tombol cetak --}}
    <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl
                   transition hover:-translate-y-0.5"
            style="background:#16a34a;">
        <i class="ti ti-printer"></i>
        Cetak Invoice
    </button>
</div>

{{-- Invoice card — tampil di layar --}}
<div class="max-w-2xl mx-auto" id="invoice-area">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Header hijau --}}
        <div class="p-8 relative overflow-hidden"
             style="background: linear-gradient(135deg, #052e16 0%, #166534 100%);">

            {{-- Dekorasi lingkaran di background --}}
            <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-10"
                 style="background:#4ade80;"></div>
            <div class="absolute -bottom-16 -left-10 w-56 h-56 rounded-full opacity-10"
                 style="background:#22c55e;"></div>

            <div class="relative z-10 flex items-start justify-between">
                {{-- Logo & nama hotel --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(255,255,255,0.15);">
                        <i class="ti ti-building text-white text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-xl">Paijo's Hotel</p>
                        <p class="text-green-300 text-xs mt-0.5">Jl. Paijo No. 1, Kota Impian</p>
                        <p class="text-green-300 text-xs">info@paijohotel.com · +62 812 3456 7890</p>
                    </div>
                </div>

                {{-- Info invoice --}}
                <div class="text-right">
                    <p class="text-green-300 text-xs mb-1 uppercase tracking-widest font-semibold">
                        Invoice
                    </p>
                    <p class="text-white font-mono font-bold text-lg">
                        {{ $booking->invoice->invoice_number ?? '-' }}
                    </p>
                    <p class="text-green-300 text-xs mt-2">
                        Tanggal: {{ now()->format('d M Y') }}
                    </p>
                    <p class="text-green-300 text-xs">
                        Jatuh Tempo:
                        {{ $booking->invoice->due_date
                            ? \Carbon\Carbon::parse($booking->invoice->due_date)->format('d M Y')
                            : '-' }}
                    </p>
                </div>
            </div>

            {{-- Badge status pembayaran di header --}}
            <div class="relative z-10 mt-6">
                @if($booking->invoice && $booking->invoice->status === 'paid')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold"
                          style="background:rgba(74,222,128,0.2); color:#4ade80;
                                 border:1px solid rgba(74,222,128,0.3);">
                        <i class="ti ti-circle-check"></i> LUNAS
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold"
                          style="background:rgba(251,191,36,0.2); color:#fbbf24;
                                 border:1px solid rgba(251,191,36,0.3);">
                        <i class="ti ti-clock"></i> BELUM LUNAS
                    </span>
                @endif
            </div>
        </div>

        <div class="p-8">

            {{-- Info tamu & detail booking --}}
            <div class="grid grid-cols-2 gap-8 mb-8">

                {{-- Tagihan kepada --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-3"
                       style="color:#16a34a;">
                        Tagihan Kepada
                    </p>
                    <p class="font-bold text-gray-800 text-base">{{ $booking->guest_name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $booking->guest_email }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->guest_phone }}</p>
                    @if($booking->guest_id_card)
                        <p class="text-sm text-gray-500">KTP: {{ $booking->guest_id_card }}</p>
                    @endif
                </div>

                {{-- Detail booking --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-3"
                       style="color:#16a34a;">
                        Detail Booking
                    </p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex gap-3">
                            <span class="text-gray-400 w-24 flex-shrink-0">Kode</span>
                            <span class="font-mono font-bold text-gray-800">
                                {{ $booking->booking_code }}
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-gray-400 w-24 flex-shrink-0">Kamar</span>
                            <span class="font-semibold text-gray-700">
                                {{ $booking->room->name ?? '-' }}
                                (No. {{ $booking->room->room_number ?? '-' }})
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-gray-400 w-24 flex-shrink-0">Check-in</span>
                            <span class="text-gray-700">
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-gray-400 w-24 flex-shrink-0">Check-out</span>
                            <span class="text-gray-700">
                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-gray-400 w-24 flex-shrink-0">Tipe</span>
                            <span class="text-gray-700">
                                {{ $booking->booking_type === 'online' ? 'Online' : 'Walk-in' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel item --}}
            @php
                $nights = \Carbon\Carbon::parse($booking->check_in)
                                        ->diffInDays($booking->check_out);
            @endphp

            <div class="rounded-2xl overflow-hidden border border-gray-100 mb-6">

                {{-- Header tabel --}}
                <div class="grid grid-cols-12 gap-4 px-5 py-3 text-xs font-bold uppercase tracking-wide"
                     style="background:#f0fdf4; color:#16a34a;">
                    <div class="col-span-6">Item</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">Harga</div>
                    <div class="col-span-2 text-right">Subtotal</div>
                </div>

                {{-- Baris kamar --}}
                <div class="grid grid-cols-12 gap-4 px-5 py-4 border-b border-gray-50
                            hover:bg-gray-50 transition">
                    <div class="col-span-6">
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $booking->room->name ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Kamar · {{ $nights }} malam ×
                            Rp {{ number_format($booking->room->price_per_night ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="col-span-2 text-center text-sm text-gray-600 flex items-center justify-center">
                        {{ $nights }}
                    </div>
                    <div class="col-span-2 text-right text-sm text-gray-600 flex items-center justify-end">
                        Rp {{ number_format($booking->room->price_per_night ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="col-span-2 text-right text-sm font-semibold text-gray-800 flex items-center justify-end">
                        Rp {{ number_format($booking->room_price, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Baris FnB --}}
                @foreach($booking->bookingItems as $item)
                <div class="grid grid-cols-12 gap-4 px-5 py-4 border-b border-gray-50
                            hover:bg-gray-50 transition">
                    <div class="col-span-6">
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $item->fnbItem->name ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            F&B ·
                            {{ $item->fnbItem->category->icon ?? '' }}
                            {{ $item->fnbItem->category->name ?? '' }}
                        </p>
                    </div>
                    <div class="col-span-2 text-center text-sm text-gray-600 flex items-center justify-center">
                        {{ $item->quantity }}
                    </div>
                    <div class="col-span-2 text-right text-sm text-gray-600 flex items-center justify-end">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                    <div class="col-span-2 text-right text-sm font-semibold text-gray-800 flex items-center justify-end">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach

                {{-- Baris total --}}
                @if($booking->invoice)
                <div class="px-5 py-4 space-y-2" style="background:#fafafa;">

                    {{-- Subtotal --}}
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($booking->invoice->subtotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- Diskon jika ada --}}
                    @if($booking->invoice->discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($booking->invoice->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    {{-- Pajak --}}
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Pajak (11%)</span>
                        <span>Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}</span>
                    </div>

                    {{-- Grand total --}}
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="font-bold text-gray-800">Grand Total</span>
                        <span class="text-xl font-bold" style="color:#16a34a;">
                            Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Catatan --}}
            @if($booking->special_requests)
                <div class="p-4 rounded-xl mb-6"
                     style="background:#fffbeb; border:1px solid #fef3c7;">
                    <p class="text-xs font-bold text-amber-700 mb-1">
                        <i class="ti ti-note mr-1"></i> Catatan Khusus
                    </p>
                    <p class="text-sm text-amber-800">{{ $booking->special_requests }}</p>
                </div>
            @endif

            {{-- Footer invoice --}}
            <div class="border-t border-gray-100 pt-6 flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-400">
                        Terima kasih telah menginap di Paijo's Hotel
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Pertanyaan? Hubungi kami di info@paijohotel.com
                    </p>
                </div>

                {{-- Tanda tangan / stempel --}}
                <div class="text-center">
                    <div class="w-20 h-16 rounded-xl border-2 border-dashed border-gray-200
                                flex items-center justify-center mb-1">
                        <i class="ti ti-writing text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-xs text-gray-400">Tanda Tangan</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     CSS KHUSUS UNTUK CETAK
     @media print = hanya aktif saat window.print()
     Semua yang tidak perlu saat cetak disembunyikan
============================================ --}}
<style>
    @media print {
        /* Sembunyikan elemen yang tidak perlu saat cetak */
        nav,                    /* navbar sidebar */
        header,                 /* topbar */
        aside,                  /* sidebar */
        .no-print,              /* class custom untuk sembunyikan */
        button {                /* semua tombol termasuk cetak */
            display: none !important;
        }

        /* Halaman cetak tidak butuh margin/padding extra */
        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Invoice area mengisi seluruh halaman */
        #invoice-area {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Pastikan warna background tetap tercetak */
        /* Beberapa browser strip background saat cetak */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Hilangkan border radius saat cetak agar rapi */
        .rounded-2xl, .rounded-xl {
            border-radius: 0 !important;
        }

        /* Breadcrumb dan tombol di atas tidak perlu dicetak */
        .flex.items-center.justify-between.mb-6 {
            display: none !important;
        }

        /* Content area tidak perlu padding saat cetak */
        main {
            padding: 0 !important;
        }
    }
</style>

@endsection