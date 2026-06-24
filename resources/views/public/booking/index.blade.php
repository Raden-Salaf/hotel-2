<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paijo's Hotel — Nikmati Pengalaman Menginap Terbaik</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ============================================
           ANIMASI HALAMAN PUBLIK
        ============================================ */

        /* Fade in dari bawah — untuk elemen yang muncul saat scroll */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Fade in biasa */
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Gerak mengambang naik turun — untuk dekorasi */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        /* Shimmer background bergerak */
        @keyframes shimmerBg {
            0%   { background-position: 200% center; }
            100% { background-position: -200% center; }
        }

        /* Partikel naik */
        @keyframes rise {
            0%   { transform: translateY(0) scale(1); opacity: 0.6; }
            100% { transform: translateY(-100px) scale(0); opacity: 0; }
        }

        /* Kelas animasi */
        .animate-fade-up   { animation: fadeUp 0.7s ease both; }
        .animate-fade-in   { animation: fadeIn 0.5s ease both; }
        .animate-float     { animation: float 4s ease-in-out infinite; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Animasi reveal saat scroll menggunakan Intersection Observer */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Gradient text hijau */
        .text-gradient {
            background: linear-gradient(135deg, #16a34a, #4ade80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Navbar blur effect */
        .navbar-blur {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.85);
        }

        /* Card hover lift */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Partikel dekoratif */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(74, 222, 128, 0.4);
            animation: rise 5s ease-out infinite;
        }
    </style>
</head>

<body class="bg-white font-sans antialiased">

{{-- ============================================
     NAVBAR — sticky dengan blur effect
============================================ --}}
<nav class="fixed top-0 left-0 right-0 z-50 navbar-blur border-b border-gray-100"
     x-data="{ scrolled: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
     :class="scrolled ? 'shadow-sm' : ''">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: #16a34a;">
                <i class="ti ti-building text-white text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800 text-sm leading-tight">Paijo's Hotel</p>
                <p class="text-xs" style="color: #16a34a;">Premium Stay Experience</p>
            </div>
        </div>

        {{-- Menu navigasi --}}
        <div class="hidden md:flex items-center gap-6">
            <a href="#fasilitas" class="text-sm text-gray-500 hover:text-green-600 transition font-medium">Fasilitas</a>
            <a href="#kamar" class="text-sm text-gray-500 hover:text-green-600 transition font-medium">Kamar</a>
            <a href="#kontak" class="text-sm text-gray-500 hover:text-green-600 transition font-medium">Kontak</a>
        </div>

        {{-- Tombol kanan --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('login') }}"
               class="hidden sm:flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600
                      border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="ti ti-login text-sm"></i>
                Staff
            </a>
            <a href="#kamar"
               class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
               style="background: #16a34a;">
                <i class="ti ti-calendar-plus text-sm"></i>
                Pesan Kamar
            </a>
        </div>
    </div>
</nav>

{{-- ============================================
     HERO SECTION
============================================ --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20"
         style="background: linear-gradient(160deg, #052e16 0%, #14532d 40%, #166534 100%);">

    {{-- Dekorasi background --}}
    {{-- Lingkaran besar blur di belakang --}}
    <div class="absolute top-20 right-0 w-96 h-96 rounded-full opacity-10"
         style="background: #4ade80; filter: blur(80px);"></div>
    <div class="absolute bottom-20 left-0 w-80 h-80 rounded-full opacity-10"
         style="background: #22c55e; filter: blur(60px);"></div>

    {{-- Kotak dekoratif floating --}}
    <div class="absolute top-32 right-20 w-16 h-16 rounded-2xl opacity-20 animate-float"
         style="background: rgba(74,222,128,0.3); border: 1px solid rgba(74,222,128,0.4);">
    </div>
    <div class="absolute bottom-40 right-40 w-10 h-10 rounded-xl opacity-15 animate-float delay-300"
         style="background: rgba(74,222,128,0.3);">
    </div>
    <div class="absolute top-60 left-20 w-8 h-8 rounded-lg opacity-20 animate-float delay-200"
         style="background: rgba(74,222,128,0.4);">
    </div>

    {{-- Partikel naik --}}
    <div class="particle w-2 h-2" style="bottom:10%; left:15%; animation-delay:0s; animation-duration:5s;"></div>
    <div class="particle w-1.5 h-1.5" style="bottom:5%; left:35%; animation-delay:1.5s; animation-duration:4.5s;"></div>
    <div class="particle w-2.5 h-2.5" style="bottom:15%; left:60%; animation-delay:3s; animation-duration:6s;"></div>
    <div class="particle w-1 h-1" style="bottom:8%; left:80%; animation-delay:0.8s; animation-duration:5.5s;"></div>

    {{-- Konten hero --}}
    <div class="relative z-10 max-w-6xl mx-auto px-4 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Teks kiri --}}
            <div>
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-6 animate-fade-up"
                     style="background: rgba(74,222,128,0.15); border: 1px solid rgba(74,222,128,0.3);">
                    <div class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></div>
                    <span class="text-green-300 text-xs font-semibold tracking-wide">
                        TERSEDIA SEKARANG
                    </span>
                </div>

                <h1 class="text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-up delay-100">
                    Pengalaman<br>
                    Menginap<br>
                    <span class="text-gradient">Tak Terlupakan</span>
                </h1>

                <p class="text-green-200 text-lg leading-relaxed mb-8 animate-fade-up delay-200">
                    Rasakan kenyamanan kamar premium dengan fasilitas lengkap
                    dan layanan terbaik di Paijo's Hotel.
                </p>

                {{-- CTA buttons --}}
                <div class="flex items-center gap-4 animate-fade-up delay-300">
                    <a href="#kamar"
                       class="px-7 py-3.5 text-sm font-bold text-white rounded-2xl transition hover:-translate-y-1"
                       style="background: #16a34a; box-shadow: 0 8px 24px rgba(22,163,74,0.4);">
                        <i class="ti ti-calendar-plus mr-2"></i>
                        Pesan Kamar
                    </a>
                    <a href="#fasilitas"
                       class="px-7 py-3.5 text-sm font-semibold rounded-2xl transition hover:-translate-y-1"
                       style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                        Lihat Fasilitas
                    </a>
                </div>

                {{-- Stats kecil --}}
                <div class="flex items-center gap-8 mt-12 animate-fade-up delay-400">
                    @foreach([
                        ['num' => '50+', 'label' => 'Kamar Premium'],
                        ['num' => '98%', 'label' => 'Kepuasan Tamu'],
                        ['num' => '24/7', 'label' => 'Layanan Kami'],
                    ] as $stat)
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stat['num'] }}</p>
                        <p class="text-xs text-green-300 mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Kartu kanan (dekorasi) --}}
            <div class="hidden lg:block animate-fade-up delay-300">
                <div class="relative">
                    {{-- Card utama --}}
                    <div class="bg-white rounded-3xl p-6 shadow-2xl animate-float">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                 style="background:#f0fdf4;">
                                <i class="ti ti-building-estate text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Paijo's Hotel</p>
                                <p class="text-xs text-gray-400">Premium Room</p>
                            </div>
                        </div>

                        {{-- Foto placeholder --}}
                        <div class="rounded-2xl overflow-hidden mb-4"
                             style="height: 180px; background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-building text-7xl" style="color:#bbf7d0;"></i>
                            </div>
                        </div>

                        {{-- Info singkat --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400">Mulai dari</p>
                                <p class="font-bold text-green-600 text-lg">Rp 350.000</p>
                                <p class="text-xs text-gray-400">per malam</p>
                            </div>
                            <div class="flex items-center gap-1">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="ti ti-star-filled text-amber-400 text-sm"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Badge floating "Tersedia" --}}
                    <div class="absolute -top-4 -right-4 bg-green-500 text-white text-xs font-bold
                                px-3 py-2 rounded-xl shadow-lg animate-float delay-200">
                        <i class="ti ti-check mr-1"></i> Tersedia
                    </div>

                    {{-- Badge floating review --}}
                    <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl p-3 shadow-xl animate-float delay-400">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-700">
                                TB
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Tamu Bahagia</p>
                                <p class="text-xs text-gray-400">"Sangat nyaman!"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave bottom --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 80L60 74C120 68 240 56 360 50C480 44 600 44 720 47C840 50 960 56 1080 56C1200 56 1320 50 1380 47L1440 44V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0Z"
                  fill="white"/>
        </svg>
    </div>
</section>

{{-- ============================================
     FASILITAS HOTEL
============================================ --}}
<section id="fasilitas" class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Section header --}}
        <div class="text-center mb-14 reveal">
            <span class="text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full"
                  style="background:#f0fdf4; color:#16a34a;">
                Fasilitas Kami
            </span>
            <h2 class="text-4xl font-bold text-gray-800 mt-4 mb-3">
                Semua yang Anda Butuhkan
            </h2>
            <p class="text-gray-400 max-w-md mx-auto">
                Kami menyediakan fasilitas lengkap untuk memastikan kenyamanan
                selama Anda menginap bersama kami
            </p>
        </div>

        {{-- Grid fasilitas --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                // Daftar fasilitas hotel dengan icon dan deskripsi
                $fasilitasHotel = [
                    ['icon' => 'ti-wifi',           'name' => 'Free WiFi',        'desc' => 'Internet cepat di seluruh area'],
                    ['icon' => 'ti-swimming-pool',  'name' => 'Kolam Renang',     'desc' => 'Outdoor pool dengan pemandangan indah'],
                    ['icon' => 'ti-tools-kitchen-2','name' => 'Restoran',         'desc' => 'Sajian kuliner lokal & internasional'],
                    ['icon' => 'ti-car',            'name' => 'Free Parkir',      'desc' => 'Area parkir luas & aman'],
                    ['icon' => 'ti-air-conditioning','name' => 'AC Central',      'desc' => 'Suhu nyaman di semua ruangan'],
                    ['icon' => 'ti-first-aid-kit',  'name' => 'Klinik 24 Jam',   'desc' => 'Layanan kesehatan siap membantu'],
                    ['icon' => 'ti-shirt',          'name' => 'Laundry',          'desc' => 'Layanan cuci & setrika express'],
                    ['icon' => 'ti-bell-ringing',   'name' => 'Room Service',     'desc' => 'Layanan kamar 24 jam penuh'],
                ];
            @endphp

            @foreach($fasilitasHotel as $index => $fasilitas)
            <div class="group p-5 rounded-2xl border border-gray-100 hover:border-green-200 transition reveal card-hover"
                 style="animation-delay: {{ $index * 0.1 }}s;">
                {{-- Icon --}}
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition
                            group-hover:scale-110"
                     style="background: #f0fdf4;">
                    <i class="ti {{ $fasilitas['icon'] }} text-2xl" style="color:#16a34a;"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-sm mb-1">{{ $fasilitas['name'] }}</h3>
                <p class="text-xs text-gray-400 leading-relaxed">{{ $fasilitas['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================
     SECTION KENAPA PILIH KAMI
============================================ --}}
<section class="py-20" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
    <div class="max-w-6xl mx-auto px-4">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Kiri: Teks --}}
            <div class="reveal">
                <span class="text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full"
                      style="background:white; color:#16a34a;">
                    Mengapa Kami?
                </span>
                <h2 class="text-4xl font-bold text-gray-800 mt-6 mb-4">
                    Lebih dari Sekedar<br>
                    <span class="text-gradient">Tempat Menginap</span>
                </h2>
                <p class="text-gray-500 leading-relaxed mb-8">
                    Kami berkomitmen memberikan pengalaman menginap yang berkesan
                    dengan sentuhan keramahan lokal dan standar internasional.
                </p>

                {{-- List keunggulan --}}
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'ti-shield-check', 'title' => 'Keamanan Terjamin',      'desc' => 'CCTV 24 jam dan security profesional'],
                        ['icon' => 'ti-star',         'title' => 'Pelayanan Bintang 5',    'desc' => 'Staff terlatih siap melayani dengan sepenuh hati'],
                        ['icon' => 'ti-map-pin',      'title' => 'Lokasi Strategis',       'desc' => 'Mudah dijangkau dari pusat kota'],
                        ['icon' => 'ti-thumb-up',     'title' => 'Harga Terjangkau',       'desc' => 'Kualitas premium dengan harga yang bersahabat'],
                    ] as $item)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background: white;">
                            <i class="ti {{ $item['icon'] }}" style="color:#16a34a;"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $item['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Kanan: Stats cards --}}
            <div class="grid grid-cols-2 gap-4 reveal">
                @foreach([
                    ['num' => '500+', 'label' => 'Tamu Puas',       'icon' => 'ti-users',        'color' => '#16a34a'],
                    ['num' => '50+',  'label' => 'Kamar Premium',   'icon' => 'ti-building',     'color' => '#2563eb'],
                    ['num' => '10+',  'label' => 'Tahun Pengalaman','icon' => 'ti-award',        'color' => '#d97706'],
                    ['num' => '4.9',  'label' => 'Rating Tamu',     'icon' => 'ti-star-filled',  'color' => '#dc2626'],
                ] as $stat)
                <div class="bg-white rounded-2xl p-6 text-center card-hover">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3"
                         style="background: {{ $stat['color'] }}18;">
                        <i class="ti {{ $stat['icon'] }} text-2xl" style="color:{{ $stat['color'] }};"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ $stat['num'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================
     DAFTAR KAMAR TERSEDIA
============================================ --}}
<section id="kamar" class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Section header --}}
        <div class="text-center mb-12 reveal">
            <span class="text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full"
                  style="background:#f0fdf4; color:#16a34a;">
                Pilih Kamar
            </span>
            <h2 class="text-4xl font-bold text-gray-800 mt-4 mb-3">
                Kamar Tersedia untuk Anda
            </h2>
            <p class="text-gray-400">Pilih kamar yang sesuai dengan kebutuhan dan budget Anda</p>
        </div>

        {{-- Filter kategori --}}
        <div class="flex items-center justify-center gap-3 mb-10 flex-wrap reveal">
            <a href="{{ route('public.booking.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
                      {{ !request('category') ? 'text-white shadow-lg' : 'text-gray-500 bg-gray-100 hover:bg-gray-200' }}"
               style="{{ !request('category') ? 'background:#16a34a;' : '' }}">
                Semua
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('public.booking.index', ['category' => $cat->id]) }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
                          {{ request('category') == $cat->id ? 'text-white shadow-lg' : 'text-gray-500 bg-gray-100 hover:bg-gray-200' }}"
                   style="{{ request('category') == $cat->id ? 'background:#16a34a;' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Grid kamar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 reveal card-hover">

                {{-- Foto kamar --}}
                <div class="relative overflow-hidden" style="padding-top: 62%;">
                    <div class="absolute inset-0">
                        @if($room->image)
                            <img src="{{ Storage::url($room->image) }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                                <i class="ti ti-building-estate text-7xl" style="color:#bbf7d0;"></i>
                            </div>
                        @endif

                        {{-- Overlay gradient --}}
                        <div class="absolute inset-0"
                             style="background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 60%);">
                        </div>

                        {{-- Badge kategori --}}
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-white text-gray-700 shadow-lg">
                                {{ $room->category->name ?? '-' }}
                            </span>
                        </div>

                        {{-- Harga di atas foto --}}
                        <div class="absolute bottom-4 left-4">
                            <p class="text-white text-xs opacity-80">Mulai dari</p>
                            <p class="text-white text-xl font-bold">
                                Rp {{ number_format($room->price_per_night, 0, ',', '.') }}
                            </p>
                            <p class="text-white text-xs opacity-80">per malam</p>
                        </div>
                    </div>
                </div>

                {{-- Info kamar --}}
                <div class="p-5">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">{{ $room->name }}</h3>
                    <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $room->description }}</p>

                    {{-- Detail singkat --}}
                    <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-users text-sm" style="color:#16a34a;"></i>
                            {{ $room->capacity }} Tamu
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-stairs text-sm" style="color:#16a34a;"></i>
                            Lantai {{ $room->floor ?? '-' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-door text-sm" style="color:#16a34a;"></i>
                            No. {{ $room->room_number }}
                        </span>
                    </div>

                    {{-- Fasilitas singkat --}}
                    @if($room->facilities)
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach(array_slice($room->facilities, 0, 3) as $f)
                                <span class="px-2.5 py-1 rounded-lg text-xs text-gray-500"
                                      style="background:#f8fafc;">
                                    {{ $f }}
                                </span>
                            @endforeach
                            @if(count($room->facilities) > 3)
                                <span class="px-2.5 py-1 rounded-lg text-xs text-gray-400"
                                      style="background:#f8fafc;">
                                    +{{ count($room->facilities) - 3 }} lagi
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Tombol pesan --}}
                    <a href="{{ route('public.booking.show', $room) }}"
                       class="block w-full py-3 text-center text-sm font-bold text-white rounded-2xl
                              transition hover:-translate-y-1"
                       style="background: #16a34a; box-shadow: 0 4px 15px rgba(22,163,74,0.3);">
                        <i class="ti ti-calendar-plus mr-2"></i>
                        Pesan Sekarang
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 py-20 text-center">
                <i class="ti ti-building-off text-6xl text-gray-200 block mb-4"></i>
                <p class="text-gray-400 text-lg">Tidak ada kamar tersedia saat ini</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($rooms->hasPages())
            <div class="mt-10 flex justify-center">{{ $rooms->links() }}</div>
        @endif
    </div>
</section>

{{-- ============================================
     SECTION KONTAK
============================================ --}}
<section id="kontak" class="py-20" style="background:#0f1f0f;">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <div class="reveal">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Ada Pertanyaan?<br>
                    <span class="text-gradient">Hubungi Kami</span>
                </h2>
                <p class="text-green-200 mb-8 leading-relaxed">
                    Tim kami siap membantu Anda 24 jam sehari, 7 hari seminggu.
                    Jangan ragu untuk menghubungi kami.
                </p>

                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'ti-phone',    'label' => 'Telepon',  'value' => '+62 812 3456 7890'],
                        ['icon' => 'ti-mail',     'label' => 'Email',    'value' => 'info@paijohotel.com'],
                        ['icon' => 'ti-map-pin',  'label' => 'Alamat',   'value' => 'Jl. Paijo No. 1, Kota Impian'],
                        ['icon' => 'ti-clock',    'label' => 'Jam Buka', 'value' => '24 Jam / 7 Hari'],
                    ] as $kontak)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background: rgba(74,222,128,0.15);">
                            <i class="ti {{ $kontak['icon'] }}" style="color:#4ade80;"></i>
                        </div>
                        <div>
                            <p class="text-xs text-green-400">{{ $kontak['label'] }}</p>
                            <p class="text-white font-medium text-sm">{{ $kontak['value'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA card --}}
            <div class="reveal">
                <div class="rounded-3xl p-8 text-center"
                     style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
                         style="background:#16a34a;">
                        <i class="ti ti-calendar-heart text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Siap Menginap?</h3>
                    <p class="text-green-300 text-sm mb-6">
                        Pesan kamar sekarang dan dapatkan pengalaman terbaik bersama kami
                    </p>
                    <a href="#kamar"
                       class="inline-block px-8 py-3.5 text-sm font-bold text-white rounded-2xl transition hover:-translate-y-1"
                       style="background:#16a34a; box-shadow: 0 8px 24px rgba(22,163,74,0.4);">
                        <i class="ti ti-calendar-plus mr-2"></i>
                        Pesan Kamar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="text-center py-6 text-xs border-t"
        style="background:#0a1a0a; color:#4b7a4b; border-color:rgba(255,255,255,0.05);">
    &copy; {{ date('Y') }} Paijo's Hotel — All Rights Reserved
</footer>

{{-- ============================================
     SCRIPT: Intersection Observer untuk animasi reveal
     Elemen dengan class "reveal" akan animate
     saat masuk viewport saat user scroll
============================================ --}}
<script>
    // Intersection Observer — efisien, tidak perlu hitung scroll manual
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Tambah class 'visible' → trigger CSS transition
                entry.target.classList.add('visible')
                // Setelah visible, tidak perlu observe lagi
                observer.unobserve(entry.target)
            }
        })
    }, {
        // Element mulai animate ketika 15% sudah masuk viewport
        threshold: 0.15
    })

    // Observe semua elemen dengan class 'reveal'
    document.querySelectorAll('.reveal').forEach(el => {
        observer.observe(el)
    })
</script>

</body>
</html>