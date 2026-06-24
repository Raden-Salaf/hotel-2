<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil — Paijo's Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

<div class="max-w-lg w-full">

    {{-- Card konfirmasi --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Header sukses --}}
        <div class="text-center py-10 px-6" style="background: linear-gradient(135deg, #052e16, #166534);">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                 style="background: rgba(255,255,255,0.15);">
                <i class="ti ti-circle-check text-white text-5xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">Booking Berhasil!</h1>
            <p class="text-green-200 text-sm">Terima kasih, pesanan Anda telah kami terima</p>
        </div>

        {{-- Kode booking --}}
        <div class="text-center py-5 border-b border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-400 mb-1">Kode Booking Anda</p>
            <p class="text-2xl font-mono font-bold tracking-widest" style="color:#16a34a;">
                {{ $booking->booking_code }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Simpan kode ini untuk referensi</p>
        </div>

        {{-- Detail booking --}}
        <div class="p-6 space-y-4">

            {{-- Info kamar --}}
            <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f0fdf4;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:#16a34a;">
                    <i class="ti ti-building-estate text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $booking->room->name }}</p>
                    <p class="text-xs text-gray-500">No. {{ $booking->room->room_number }}</p>
                </div>
            </div>

            {{-- Info menginap --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-400 mb-1">Check-in</p>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $booking->check_in->format('d M Y') }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-400 mb-1">Check-out</p>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $booking->check_out->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Info tamu --}}
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Nama Tamu</span>
                    <span class="font-medium text-gray-800">{{ $booking->guest_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Email</span>
                    <span class="font-medium text-gray-800">{{ $booking->guest_email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">No. HP</span>
                    <span class="font-medium text-gray-800">{{ $booking->guest_phone }}</span>
                </div>
            </div>

            {{-- Pesanan FnB jika ada --}}
            @if($booking->bookingItems->count() > 0)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 mb-2">Pesanan F&B</p>
                @foreach($booking->bookingItems as $item)
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $item->fnbItem->name }} ×{{ $item->quantity }}</span>
                        <span class="text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Rincian biaya --}}
            @if($booking->invoice)
            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($booking->invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Pajak (11%)</span>
                    <span>Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-200">
                    <span class="text-gray-800">Total Pembayaran</span>
                    <span style="color:#16a34a;">
                        Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Status invoice --}}
                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs text-gray-400">Status Pembayaran</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                        Menunggu Pembayaran
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Batas Pembayaran</span>
                    <span class="text-xs font-medium text-red-500">
                        {{ $booking->invoice->due_date->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
            @endif

        </div>

        {{-- Footer tombol --}}
        <div class="p-6 pt-0 space-y-3">
            {{-- Tombol bayar — akan kita sambungkan ke Midtrans di fase berikutnya --}}
            <button class="w-full py-3 text-sm font-bold text-white rounded-xl"
                    style="background:#16a34a;"
                    onclick="alert('Integrasi Midtrans akan segera tersedia!')">
                <i class="ti ti-credit-card mr-2"></i>
                Bayar Sekarang
            </button>

            <a href="{{ route('public.booking.index') }}"
               class="block w-full py-3 text-sm font-medium text-center text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

</body>
</html>