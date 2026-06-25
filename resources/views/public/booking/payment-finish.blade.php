<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran — Paijo's Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

        @if($booking->invoice?->status === 'paid')
            {{-- Pembayaran berhasil --}}
            <div class="text-center py-12 px-6" style="background: linear-gradient(135deg, #052e16, #166534);">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                    style="background:rgba(255,255,255,0.2);">
                    <i class="ti ti-circle-check text-white text-5xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Berhasil!</h1>
                <p class="text-green-200 text-sm">Booking Anda telah dikonfirmasi</p>
            </div>

        @else
            {{-- Pembayaran pending / belum selesai --}}
            <div class="text-center py-12 px-6" style="background: linear-gradient(135deg, #78350f, #b45309);">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                    style="background:rgba(255,255,255,0.2);">
                    <i class="ti ti-clock text-white text-5xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Menunggu Pembayaran</h1>
                <p class="text-amber-200 text-sm">Selesaikan pembayaran Anda sebelum batas waktu</p>
            </div>
        @endif

        <div class="p-6 space-y-4">

            {{-- Kode booking --}}
            <div class="text-center p-4 rounded-xl" style="background:#f8fafc;">
                <p class="text-xs text-gray-400 mb-1">Kode Booking</p>
                <p class="font-mono font-bold text-xl" style="color:#16a34a;">
                    {{ $booking->booking_code }}
                </p>
            </div>

            {{-- Detail singkat --}}
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Kamar</span>
                    <span class="font-medium text-gray-800">{{ $booking->room->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Check-in</span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Check-out</span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex justify-between border-t border-gray-100 pt-2">
                    <span class="font-bold text-gray-800">Total</span>
                    <span class="font-bold" style="color:#16a34a;">
                        Rp {{ number_format($booking->invoice?->total ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Status --}}
            <div class="p-3 rounded-xl text-center text-sm font-semibold
                    {{ $booking->invoice?->status === 'paid'
    ? 'bg-green-100 text-green-700'
    : 'bg-yellow-100 text-yellow-700' }}">
                {{ $booking->invoice?->status === 'paid'
    ? '✓ Pembayaran Diterima — Booking Dikonfirmasi!'
    : '⏳ Menunggu Konfirmasi Pembayaran' }}
            </div>

            {{-- Info tambahan --}}
            <p class="text-xs text-center text-gray-400">
                Konfirmasi booking akan dikirim ke
                <span class="font-medium text-gray-600">{{ $booking->guest_email }}</span>
            </p>

            <a href="{{ route('public.booking.index') }}" class="block w-full py-3 text-sm font-semibold text-center text-white rounded-xl
                  transition hover:-translate-y-0.5" style="background:#16a34a;">
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>

</html>