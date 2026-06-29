<header class="flex items-center justify-between px-4 py-3 flex-shrink-0
               bg-white border-b border-gray-100">

    {{-- Kiri: Toggle sidebar + judul --}}
    <div class="flex items-center gap-3 min-w-0">
        <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 flex items-center justify-center rounded-xl
                       text-gray-400 hover:bg-gray-100 hover:text-gray-600
                       transition flex-shrink-0">
            <i class="ti ti-menu-2 text-lg"></i>
        </button>

        <div class="min-w-0">
            <h1 class="text-sm md:text-base font-semibold text-gray-800 truncate">
                @yield('title', 'Dashboard')
            </h1>
            <p class="text-xs text-gray-400 hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Kanan --}}
    <div class="flex items-center gap-2 flex-shrink-0">

        {{-- Tanggal — hanya desktop --}}
        <span class="text-xs text-gray-400 hidden lg:block">
            {{ now()->translatedFormat('l, d F Y') }}
        </span>

        {{-- Notifikasi --}}
        <button class="w-9 h-9 flex items-center justify-center rounded-xl
                       text-gray-400 hover:bg-gray-100 hover:text-gray-600
                       transition relative">
            <i class="ti ti-bell text-lg"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-green-500 rounded-full"></span>
        </button>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-100 hover:text-gray-700 rounded-xl transition">
                <i class="ti ti-logout text-base"></i>
                <span class="hidden sm:inline">Logout</span>
            </button>
        </form>
    </div>
</header>
