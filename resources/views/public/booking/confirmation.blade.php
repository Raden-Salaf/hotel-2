<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking — Paijo's Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Load Snap.js dari Midtrans — berbeda URL untuk sandbox vs production --}}
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    {{-- DEBUG SEMENTARA — hapus setelah Midtrans berfungsi --}}
    @if (isset($snapError) && $snapError)
        <div style="background:red; color:white; padding:16px; margin:16px; border-radius:8px;">
            <strong>Midtrans Error:</strong> {{ $snapError }}
        </div>
    @endif
    <div class="max-w-lg w-full">

        {{-- Card konfirmasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

            {{-- Header sukses --}}
            <div class="text-center py-10 px-6 relative overflow-hidden"
                style="background: linear-gradient(135deg, #052e16, #166534);">

                {{-- Dekorasi --}}
                <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full opacity-10" style="background:#4ade80;">
                </div>
                <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full opacity-10" style="background:#22c55e;">
                </div>

                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                        style="background:rgba(255,255,255,0.15);">
                        <i class="ti ti-calendar-check text-white text-5xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">Booking Berhasil!</h1>
                    <p class="text-green-200 text-sm">
                        Pesanan Anda telah kami terima. Selesaikan pembayaran untuk konfirmasi.
                    </p>
                </div>
            </div>

            {{-- Kode booking --}}
            <div class="text-center py-5 border-b border-gray-100" style="background:#f8fafc;">
                <p class="text-xs text-gray-400 mb-1">Kode Booking Anda</p>
                <p class="text-2xl font-mono font-bold tracking-widest" style="color:#16a34a;">
                    {{ $booking->booking_code }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Simpan kode ini untuk referensi</p>
            </div>

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
                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1">Check-out</p>
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                {{-- Info tamu --}}
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Nama</span>
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

                {{-- Pesanan FnB --}}
                @if ($booking->bookingItems->count() > 0)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 mb-2">Pesanan F&B</p>
                        @foreach ($booking->bookingItems as $item)
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">
                                    {{ $item->fnbItem->name }} ×{{ $item->quantity }}
                                </span>
                                <span class="text-gray-800">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Rincian biaya --}}
                @if ($booking->invoice)
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

                        {{-- Status pembayaran --}}
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs text-gray-400">Status</span>
                            @if ($booking->invoice->status === 'paid')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                                             bg-green-100 text-green-700">
                                    ✓ Lunas
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                                             bg-yellow-100 text-yellow-700">
                                    Menunggu Pembayaran
                                </span>
                            @endif
                        </div>

                        {{-- Batas waktu bayar --}}
                        @if ($booking->invoice->status === 'unpaid')
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">Batas Bayar</span>
                                <span class="text-xs font-medium text-red-500">
                                    {{ \Carbon\Carbon::parse($booking->invoice->due_date)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            {{-- Tombol aksi --}}
            <div class="px-6 pb-6 space-y-3">

                @if ($booking->invoice?->status === 'unpaid' && $snapToken)
                    {{-- Tombol bayar via Midtrans Snap --}}
                    <button id="pay-button"
                        class="w-full py-3.5 text-sm font-bold text-white rounded-xl transition
                                                   hover:-translate-y-0.5"
                        style="background:#16a34a;
                                                   box-shadow: 0 4px 15px rgba(22,163,74,0.3);">
                        <i class="ti ti-credit-card mr-2"></i>
                        Bayar Sekarang —
                        Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}
                    </button>

                    {{-- Info metode pembayaran yang tersedia --}}
                    <div class="flex items-center justify-center gap-3 flex-wrap">
                        <span class="text-xs text-gray-400">Bayar via:</span>
                        @foreach (['GoPay', 'OVO', 'Transfer Bank', 'Kartu Kredit', 'QRIS'] as $method)
                            <span class="text-xs px-2 py-1 bg-gray-100 rounded-lg text-gray-500">
                                {{ $method }}
                            </span>
                        @endforeach
                    </div>
                @elseif($booking->invoice?->status === 'paid')
                    {{-- Sudah lunas --}}
                    <div class="w-full py-3.5 text-center text-sm font-bold rounded-xl"
                        style="background:#f0fdf4; color:#16a34a;">
                        <i class="ti ti-circle-check mr-2"></i>
                        Pembayaran Berhasil!
                    </div>
                @else
                    {{-- Snap token tidak tersedia --}}
                    <div class="w-full py-3.5 text-center text-sm text-gray-500 bg-gray-100 rounded-xl">
                        <i class="ti ti-clock mr-2"></i>
                        Menunggu konfirmasi pembayaran...
                    </div>
                @endif

                <a href="{{ route('public.booking.index') }}"
                    class="block w-full py-3 text-sm font-medium text-center text-gray-500
                      bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>

    {{-- ============================================
    SCRIPT MIDTRANS SNAP
    snap.pay() membuka popup pembayaran Midtrans
    ============================================ --}}
    @if ($snapToken)
        <script>
            let isSnapOpen = false

            function initPayButton() {
                const btn = document.getElementById('pay-button')
                if (!btn) return

                btn.addEventListener('click', function() {
                    if (typeof snap === 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sistem belum siap',
                            text: 'Mohon tunggu sebentar lalu coba lagi.',
                        })
                        return
                    }

                    if (isSnapOpen) return
                    isSnapOpen = true

                    this.disabled = true
                    this.innerHTML = '<i class="ti ti-loader mr-2"></i> Membuka pembayaran...'

                    const btn = this

                    snap.pay('{{ $snapToken }}', {

                        // Pembayaran berhasil — langsung redirect ke finish
                        onSuccess: function(result) {
                            isSnapOpen = false
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Memverifikasi pembayaran...',
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                            }).then(() => {
                                // Paksa navigasi ke URL baru — tidak pakai history/cache
                                window.location.replace(
                                    '{{ route('public.payment.finish', $booking->id) }}')
                            })
                        },

                        onPending: function(result) {
                            isSnapOpen = false
                            window.location.replace('{{ route('public.payment.finish', $booking->id) }}')
                        },

                        // Pembayaran pending (transfer bank dll)
                        onPending: function(result) {
                            isSnapOpen = false

                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Selesaikan pembayaran Anda sesuai instruksi.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            }).then(() => {
                                window.location.href =
                                    '{{ route('public.payment.finish', $booking->id) }}'
                            })
                        },

                        // Pembayaran gagal
                        onError: function(result) {
                            isSnapOpen = false
                            btn.disabled = false
                            btn.innerHTML =
                                '<i class="ti ti-credit-card mr-2"></i> Bayar Sekarang — Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}'

                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Silakan coba metode pembayaran lain.',
                            })
                        },

                        // Popup ditutup tanpa bayar
                        onClose: function() {
                            isSnapOpen = false
                            btn.disabled = false
                            btn.innerHTML =
                                '<i class="ti ti-credit-card mr-2"></i> Bayar Sekarang — Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}'
                        }
                    })
                })
            }

            // Tunggu semua script selesai load
            window.addEventListener('load', initPayButton)
        </script>
    @endif

</body>

</html>
