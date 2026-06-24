<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Paijo's Hotel</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite: Tailwind + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ============================================
           ANIMASI — ini adalah CSS @keyframes
           Setiap animasi punya nama, dan dipanggil
           via property "animation" di elemen HTML
        ============================================ */

        /* Partikel naik ke atas lalu menghilang */
        @keyframes floatParticle {
            0%   { transform: translateY(0) translateX(0); opacity: 0.7; }
            100% { transform: translateY(-200px) translateX(15px); opacity: 0; }
        }

        /* Kotak dekoratif berkedip pelan */
        @keyframes shimmer {
            0%, 100% { opacity: 0.08; }
            50%       { opacity: 0.25; }
        }

        /* Logo berdenyut naik-turun */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.06); }
        }

        /* Panel kiri muncul dari bawah */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Form login muncul dari kanan */
        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Dot navigasi bergerak */
        @keyframes dotPulse {
            0%, 100% { transform: scaleX(1); opacity: 0.5; }
            50%       { transform: scaleX(1.3); opacity: 1; }
        }

        /* ============================================
           KELAS ANIMASI — diterapkan ke elemen
        ============================================ */

        .animate-slide-up   { animation: slideUp 0.7s ease both; }
        .animate-slide-left { animation: slideLeft 0.6s ease 0.15s both; }

        /* Particle dot */
        .particle {
            position: absolute;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #22c55e;
            animation: floatParticle 5s ease-out infinite;
            pointer-events: none;
        }

        /* Kotak dekoratif background */
        .deco-box {
            position: absolute;
            border-radius: 8px;
            background: #16a34a;
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Logo hotel berdenyut */
        .logo-pulse {
            animation: pulse 3s ease-in-out infinite;
        }

        /* Dot navigasi kiri */
        .nav-dot {
            height: 6px;
            border-radius: 3px;
            background: #166534;
            transition: all 0.3s;
        }
        .nav-dot.active {
            background: #22c55e;
            width: 20px !important;
        }

        /* Input focus ring hijau */
        .input-field:focus {
            outline: none;
            border-color: #22c55e !important;
            background: white !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        /* Tombol login hover effect */
        .btn-login {
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: #15803d !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: none;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">

{{-- ============================================
     WRAPPER UTAMA — full screen, flex berdampingan
     Alpine.js x-data untuk state show password
============================================ --}}
<div class="min-h-screen flex" x-data="{ showPass: false }">

    {{-- ==========================================
         PANEL KIRI — Branding & Animasi
         bg gelap #0a1a0a = hijau sangat gelap
    ========================================== --}}
    <div class="hidden lg:flex w-5/12 relative flex-col items-center justify-center p-10 overflow-hidden"
         style="background-color: #0a1a0a;">

        {{-- Kotak dekoratif di background --}}
        <div class="deco-box w-24 h-24 top-8 left-4"   style="transform: rotate(15deg);  animation-delay: 0s;"></div>
        <div class="deco-box w-14 h-14 top-1/3 right-6" style="transform: rotate(-12deg); animation-delay: 0.7s;"></div>
        <div class="deco-box w-36 h-36 bottom-4 -left-8" style="transform: rotate(25deg); animation-delay: 1.3s;"></div>
        <div class="deco-box w-10 h-10 bottom-1/3 right-10" style="transform: rotate(-20deg); animation-delay: 0.4s;"></div>
        <div class="deco-box w-16 h-16 top-3/5 left-1/3" style="transform: rotate(8deg);  animation-delay: 1.8s;"></div>

        {{-- Partikel hijau yang naik ke atas --}}
        <div class="particle" style="bottom: 5%;  left: 15%; animation-delay: 0s;    animation-duration: 5s;"></div>
        <div class="particle" style="bottom: 10%; left: 45%; animation-delay: 1.5s;  animation-duration: 4.5s;"></div>
        <div class="particle" style="bottom: 0%;  left: 70%; animation-delay: 3s;    animation-duration: 6s;"></div>
        <div class="particle" style="bottom: 15%; left: 30%; animation-delay: 0.8s;  animation-duration: 5.5s;"></div>
        <div class="particle" style="bottom: 5%;  left: 80%; animation-delay: 2.2s;  animation-duration: 4.8s;"></div>
        <div class="particle" style="bottom: 20%; left: 55%; animation-delay: 1.1s;  animation-duration: 5.2s;" style="width:3px; height:3px;"></div>

        {{-- Konten tengah panel kiri --}}
        <div class="relative z-10 text-center animate-slide-up">

            {{-- Logo box — ganti icon dengan <img> kalau sudah punya logo asli --}}
            {{-- Contoh: <img src="{{ asset('images/logo.png') }}" class="w-9 h-9 object-contain"> --}}
            <div class="logo-pulse w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6"
                 style="background-color: #22c55e;">
                <i class="ti ti-building text-white" style="font-size: 38px;"></i>
            </div>

            {{-- Nama hotel --}}
            <h1 class="text-3xl font-bold text-white leading-tight">
                Paijo's<br>
                <span style="color: #4ade80;">Hotel</span>
            </h1>

            {{-- Tagline --}}
            <p class="mt-4 text-sm leading-relaxed" style="color: #4b7a4b;">
                Sistem manajemen hotel<br>yang modern & efisien
            </p>

            {{-- Fitur-fitur kecil --}}
            <div class="mt-8 space-y-2 text-left">
                @foreach([
                    ['icon' => 'ti-calendar-check', 'text' => 'Kelola booking online & walk-in'],
                    ['icon' => 'ti-tools-kitchen-2', 'text' => 'Manajemen F&B terintegrasi'],
                    ['icon' => 'ti-receipt',         'text' => 'Invoice & pembayaran otomatis'],
                ] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: rgba(34,197,94,0.15);">
                        <i class="ti {{ $feature['icon'] }}" style="color: #4ade80; font-size: 14px;"></i>
                    </div>
                    <span class="text-xs" style="color: #4b7a4b;">{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Dot navigasi dekoratif --}}
            <div class="flex items-center justify-center gap-2 mt-8">
                <div class="nav-dot active" style="width: 20px;"></div>
                <div class="nav-dot" style="width: 6px;"></div>
                <div class="nav-dot" style="width: 6px;"></div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         PANEL KANAN — Form Login
    ========================================== --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white">

        <div class="w-full max-w-sm animate-slide-left">

            {{-- Header form --}}
            <div class="mb-8">
                {{-- Logo kecil untuk mobile (panel kiri hilang di layar kecil) --}}
                <div class="flex items-center gap-3 mb-6 lg:hidden">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background: #22c55e;">
                        <i class="ti ti-building text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-800">Paijo's Hotel</span>
                </div>

                <p class="text-sm text-gray-400 mb-1">Selamat datang kembali 👋</p>
                <h2 class="text-2xl font-bold text-gray-800">
                    Masuk ke <span style="color: #16a34a;">Dashboard</span>
                </h2>
            </div>

            {{-- ============================================
                 FORM LOGIN
                 action="{{ route('login') }}" → kirim ke route 'login' yang sudah ada dari Breeze
                 method="POST" → HTTP POST
                 @csrf → wajib ada untuk keamanan (Cross-Site Request Forgery protection)
            ============================================ --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Field Email --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="email">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="admin@paijohotel.com"
                            class="input-field w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-800 placeholder-gray-400
                                   @error('email') border-red-400 bg-red-50 @enderror">
                    </div>
                    {{-- Tampilkan error email jika ada --}}
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <i class="ti ti-alert-circle text-sm"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Field Password --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="password">
                        Password
                    </label>
                    {{-- Alpine.js: x-bind:type ubah type input berdasarkan state showPass --}}
                    <div class="relative">
                        <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input
                            id="password"
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            required
                            placeholder="Masukkan password"
                            class="input-field w-full pl-9 pr-10 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-800 placeholder-gray-400
                                   @error('password') border-red-400 bg-red-50 @enderror">
                        {{-- Toggle show/hide password --}}
                        <button type="button"
                                @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i :class="showPass ? 'ti ti-eye-off' : 'ti ti-eye'" class="text-base"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <i class="ti ti-alert-circle text-sm"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="remember"
                               class="w-3.5 h-3.5 rounded"
                               style="accent-color: #22c55e;">
                        <span class="text-xs text-gray-500">Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs font-medium hover:underline"
                           style="color: #16a34a;">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- Tombol submit --}}
                <button type="submit"
                        class="btn-login w-full py-3 text-sm font-semibold text-white rounded-xl"
                        style="background-color: #16a34a;">
                    <i class="ti ti-login mr-2"></i>
                    Masuk Sekarang
                </button>

            </form>

            {{-- Footer --}}
            <p class="text-center text-xs text-gray-400 mt-8">
                Paijo's Hotel &copy; {{ date('Y') }} &middot;
                <span style="color: #16a34a;">Management System v1.0</span>
            </p>

        </div>
    </div>

</div>

</body>
</html>