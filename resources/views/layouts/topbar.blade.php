<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">

    {{-- Tombol toggle sidebar --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page Title --}}
    <div class="flex-1 ml-4">
        <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
    </div>

    {{-- Kanan: Notifikasi + Logout --}}
    <div class="flex items-center gap-3">

        {{-- Tanggal hari ini --}}
        <span class="text-sm text-gray-500 hidden md:block">
            {{ now()->translatedFormat('l, d F Y') }}
        </span>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</header>