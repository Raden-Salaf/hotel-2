<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran — Paijo's Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn 0.5s ease both;
        }

        .animate-fade-up {
            animation: fadeUp 0.5s ease 0.3s both;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">

        {{-- ==========================================
         Cek status invoice langsung dari DB
         $booking sudah di-fresh() di controller
    ========================================== --}}
        @if ($booking->invoice?->status === 'paid')

            {{-- ===== PEMBAYARAN BERHASIL ===== --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

                {{-- Header sukses --}}
                <div class="text-center py-10 px-6 relative overflow-hidden"
                    style="background: linear-gradient(135deg, #052e16, #166534);">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full opacity-10" style="background:#4ade80;">
                    </div>
                    <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full opacity-10"
                        style="background:#22c55e;"></div>

                    <div class="relative z-10">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4
                            animate-scale-in"
                            style="background:rgba(74,222,128,0.2);
                            border:2px solid rgba(74,222,128,0.4);">
                            <i class="ti ti-circle-check text-5xl" style="color:#4ade80;"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-white mb-2 animate-fade-up">
                            Pembayaran Berhasil!
                        </h1>
                        <p class="text-green-200 text-sm animate-fade-up">
                            Booking Anda telah dikonfirmasi
                        </p>
                    </div>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Kode booking --}}
                    <div class="text-center p-4 rounded-2xl" style="background:#f0fdf4;">
                        <p class="text-xs text-gray-400 mb-1">Kode Booking</p>
                        <p class="font-mono font-bold text-2xl tracking-widest" style="color:#16a34a;">
                            {{ $booking->booking_code }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Tunjukkan kode ini saat check-in
                        </p>
                    </div>

                    {{-- Detail booking --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                style="background:#16a34a;">
                                <i class="ti ti-building-estate text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $booking->room->name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    No. {{ $booking->room->room_number }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-gray-50">
                                <p class="text-xs text-gray-400 mb-0.5">Check-in</p>
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50">
                                <p class="text-xs text-gray-400 mb-0.5">Check-out</p>
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Nama</span>
                                <span class="font-medium text-gray-800">{{ $booking->guest_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Email</span>
                                <span class="font-medium text-gray-800 text-xs">
                                    {{ $booking->guest_email }}
                                </span>
                            </div>
                        </div>

                        {{-- Rincian pembayaran --}}
                        @if ($booking->invoice)
                            <div class="border-t border-gray-100 pt-3 space-y-2 text-sm">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal</span>
                                    <span>Rp {{ number_format($booking->invoice->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Pajak (11%)</span>
                                    <span>Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-100">
                                    <span class="text-gray-800">Total Dibayar</span>
                                    <span style="color:#16a34a;">
                                        Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Badge lunas --}}
                        <div class="flex items-center justify-center gap-2 py-3 rounded-xl
                            font-semibold text-sm"
                            style="background:#f0fdf4; color:#16a34a;">
                            <i class="ti ti-circle-check text-lg"></i>
                            Invoice Lunas — Booking Dikonfirmasi
                        </div>

                        <p class="text-xs text-center text-gray-400">
                            Detail booking dikirim ke
                            <span class="font-medium text-gray-600">{{ $booking->guest_email }}</span>
                        </p>
                    </div>

                    {{-- Tombol --}}
                    <a href="{{ route('public.booking.index') }}"
                        class="block w-full py-3 text-sm font-bold text-center text-white rounded-xl
                      transition hover:-translate-y-0.5 mt-4"
                        style="background:#16a34a;">
                        <i class="ti ti-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @else
            {{-- ===== PEMBAYARAN PENDING ===== --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

                <div class="text-center py-10 px-6 relative overflow-hidden"
                    style="background: linear-gradient(135deg, #78350f, #b45309);">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full opacity-10" style="background:#fbbf24;">
                    </div>
                    <div class="relative z-10">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                            style="background:rgba(251,191,36,0.2);
                            border:2px solid rgba(251,191,36,0.3);">
                            <i class="ti ti-clock text-5xl" style="color:#fbbf24;"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-white mb-2">Menunggu Pembayaran</h1>
                        <p class="text-amber-200 text-sm">
                            Selesaikan pembayaran Anda sebelum batas waktu
                        </p>
                    </div>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Kode booking --}}
                    <div class="text-center p-4 rounded-2xl bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1">Kode Booking</p>
                        <p class="font-mono font-bold text-2xl tracking-widest" style="color:#16a34a;">
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
                        <div class="flex justify-between border-t border-gray-100 pt-2">
                            <span class="font-bold text-gray-800">Total</span>
                            <span class="font-bold" style="color:#16a34a;">
                                Rp {{ number_format($booking->invoice?->total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Batas waktu --}}
                    @if ($booking->invoice?->due_date)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-red-50 border border-red-100">
                            <i class="ti ti-alarm text-red-500 text-lg flex-shrink-0"></i>
                            <div>
                                <p class="text-xs font-semibold text-red-700">Batas Waktu Pembayaran</p>
                                <p class="text-sm font-bold text-red-600">
                                    {{ \Carbon\Carbon::parse($booking->invoice->due_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Info cara konfirmasi VA --}}
                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            <i class="ti ti-info-circle mr-1"></i>
                            Sudah transfer via Virtual Account?
                        </p>
                        <p class="text-xs text-blue-600">
                            Klik tombol <strong>"Cek Status Pembayaran"</strong> di bawah
                            setelah konfirmasi di simulator Midtrans.
                        </p>
                    </div>

                    {{-- Tombol --}}
                    <div class="space-y-2 pt-2">

                        {{-- Cek status --}}
                        <button id="btn-check" onclick="checkPaymentStatus()"
                            class="w-full py-3 text-sm font-bold text-white rounded-xl
                               transition hover:-translate-y-0.5"
                            style="background:#3b82f6;">
                            <i class="ti ti-refresh mr-2"></i>
                            Cek Status Pembayaran
                        </button>

                        {{-- Bayar ulang --}}
                        <a href="{{ route('public.booking.confirmation', $booking->id) }}"
                            class="block w-full py-3 text-sm font-bold text-center text-white rounded-xl
                          transition hover:-translate-y-0.5"
                            style="background:#16a34a;">
                            <i class="ti ti-credit-card mr-2"></i>
                            Lanjutkan / Bayar Ulang
                        </a>

                        <a href="{{ route('public.booking.index') }}"
                            class="block w-full py-3 text-sm font-medium text-center text-gray-500
                          bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>

            {{-- Script cek status — hanya load saat invoice belum paid --}}
            <script>
                function checkPaymentStatus() {
                    const btn = document.getElementById('btn-check')
                    btn.disabled = true
                    btn.innerHTML = '<i class="ti ti-loader mr-2"></i> Mengecek status...'

                    // Fetch ke endpoint cek status kita
                    fetch('{{ route('public.payment.check', $booking->id) }}')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Berhasil — tampilkan SweetAlert lalu hard reload
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Terkonfirmasi!',
                                    text: 'Invoice Anda telah lunas.',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                }).then(() => {
                                    // Hard reload dengan cache bust agar data fresh
                                    window.location.href = window.location.href.split('?')[0] +
                                        '?t=' + Date.now()
                                })
                            } else {
                                // Masih pending
                                btn.disabled = false
                                btn.innerHTML = '<i class="ti ti-refresh mr-2"></i> Cek Status Pembayaran'

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Belum Terkonfirmasi',
                                    html: 'Pastikan Anda sudah konfirmasi di ' +
                                        '<a href="https://simulator.sandbox.midtrans.com" ' +
                                        'target="_blank" class="text-blue-600 underline">' +
                                        'simulator Midtrans</a>.',
                                })
                            }
                        })
                        .catch(err => {
                            btn.disabled = false
                            btn.innerHTML = '<i class="ti ti-refresh mr-2"></i> Cek Status Pembayaran'

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengecek',
                                text: 'Terjadi kesalahan koneksi. Coba lagi.',
                            })
                        })
                }
            </script>

        @endif

    </div>

</body>

</html>
