<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>

    {{-- Tabler Icons untuk icon modern --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite compile Tailwind + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js untuk interaktivitas ringan (toggle sidebar, dll) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert2 by Rashid - untuk semua alert & konfirmasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script global untuk flash message otomatis pakai SweetAlert --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    {{-- @json() otomatis escape karakter kutip, &, <, > dll --}}
                    {{-- jauh lebih aman dari langsung nulis {{ session('success') }} --}}
                    text: @json(session('success')),
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                })
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error')),
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                })
            @endif

        })
    </script>

    @stack('scripts')
</head>

<body class="bg-gray-50 font-sans antialiased">

    {{-- x-data di sini agar sidebarOpen bisa diakses sidebar & topbar --}}
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">

        @include('layouts.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden min-w-0">

            @include('layouts.topbar')

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">

                {{-- Flash message success
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                        <i class="ti ti-circle-check text-green-500 text-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif
                    !! ini 
                Flash message error
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                        <i class="ti ti-alert-circle text-red-500 text-lg"></i>
                        {{ session('error') }}
                    </div>
                @endif --}}

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
