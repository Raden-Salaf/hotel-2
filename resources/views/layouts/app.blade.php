<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite: Tailwind + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 font-sans antialiased">

    {{-- Wrapper utama --}}
    <div x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        isMobile: window.innerWidth < 1024,
        init() {
            this.$watch('isMobile', val => {
                if (!val) this.sidebarOpen = true
                else this.sidebarOpen = false
            })
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024
            })
        }
     }" class="flex h-screen overflow-hidden">

        {{-- Overlay gelap saat sidebar mobile terbuka --}}
        <div x-show="sidebarOpen && isMobile" @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden">
        </div>

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main content --}}
        <div class="flex flex-col flex-1 overflow-hidden min-w-0">

            {{-- Topbar --}}
            @include('layouts.topbar')

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">

                @yield('content')
            </main>
        </div>
    </div>

    {{-- SweetAlert flash messages --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                })
            @endif

            @if(session('error'))
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

    {{-- Stack scripts dari masing-masing halaman --}}
    @stack('scripts')

</body>

</html>
