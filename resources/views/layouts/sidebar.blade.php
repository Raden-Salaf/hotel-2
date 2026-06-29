{{-- Sidebar — fixed di mobile, relative di desktop --}}
<aside :class="{
            'w-60': sidebarOpen,
            'w-0 overflow-hidden lg:w-16': !sidebarOpen
        }" class="flex flex-col flex-shrink-0 transition-all duration-300 ease-in-out z-30
              fixed inset-y-0 left-0 lg:relative" style="background-color: #0f1f0f;">

    {{-- Logo area --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b flex-shrink-0" style="border-color: rgba(255,255,255,0.08);">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
            style="background-color: #22c55e;">
            {{-- Slot logo hotel --}}
            {{-- Ganti dengan: <img src="{{ asset('images/logo.png') }}" class="w-7 h-7 object-contain"> --}}
            <i class="ti ti-building text-white text-xl"></i>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
            class="min-w-0">
            <p class="text-white font-semibold text-sm leading-tight whitespace-nowrap">
                Paijo's Hotel
            </p>
            <p class="text-xs mt-0.5 whitespace-nowrap" style="color:#4ade80;">
                Management System
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-2 py-3 overflow-y-auto space-y-0.5 overflow-x-hidden">

        {{-- Label section --}}
        <div x-show="sidebarOpen" class="px-2 pt-2 pb-1">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#2d5a2d;">Utama</p>
        </div>

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                  {{ request()->routeIs('dashboard') ? 'text-green-200' : 'hover:text-green-100' }}"
            style="{{ request()->routeIs('dashboard') ? 'background-color:#166534;' : '' }}"
            onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
            onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
            <i class="ti ti-layout-dashboard flex-shrink-0 text-lg"
                style="color:{{ request()->routeIs('dashboard') ? '#86efac' : '#4b7a4b' }};"></i>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                Dashboard
            </span>
        </a>

        {{-- ===== SUPER ADMIN ===== --}}
        @role('super_admin')
        <div x-show="sidebarOpen" class="px-2 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#2d5a2d;">Manajemen</p>
        </div>

        @php
            $navItems = [
                ['route' => 'admin.rooms.index', 'match' => 'admin.rooms.*', 'icon' => 'ti-building-estate', 'label' => 'Kamar'],
                ['route' => 'admin.room-categories.index', 'match' => 'admin.room-categories.*', 'icon' => 'ti-tag', 'label' => 'Kategori Kamar'],
                ['route' => 'admin.fnb-categories.index', 'match' => 'admin.fnb-categories.*', 'icon' => 'ti-category', 'label' => 'Kategori F&B'],
                ['route' => 'admin.fnb-items.index', 'match' => 'admin.fnb-items.*', 'icon' => 'ti-tools-kitchen-2', 'label' => 'Menu F&B'],
                ['route' => 'admin.laundry.index', 'match' => 'admin.laundry.*', 'icon' => 'ti-wash', 'label' => 'Item Laundry'],
                ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'ti-users', 'label' => 'Kelola User'],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                          {{ request()->routeIs($item['match']) ? 'text-green-200' : 'hover:text-green-100' }}"
                style="{{ request()->routeIs($item['match']) ? 'background-color:#166534;' : '' }}"
                onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
                onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
                <i class="ti {{ $item['icon'] }} flex-shrink-0 text-lg"
                    style="color:{{ request()->routeIs($item['match']) ? '#86efac' : '#4b7a4b' }};"></i>
                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
        @endrole

        {{-- ===== RESEPSIONIS ===== --}}
        @role('resepsionis|super_admin')
        <div x-show="sidebarOpen" class="px-2 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#2d5a2d;">Resepsionis</p>
        </div>

        {{-- Booking --}}
        <a href="{{ route('resepsionis.bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                  {{ request()->routeIs('resepsionis.bookings.*') ? 'text-green-200' : 'hover:text-green-100' }}"
            style="{{ request()->routeIs('resepsionis.bookings.*') ? 'background-color:#166534;' : '' }}"
            onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
            onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
            <i class="ti ti-calendar-check flex-shrink-0 text-lg"
                style="color:{{ request()->routeIs('resepsionis.bookings.*') ? '#86efac' : '#4b7a4b' }};"></i>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                Booking
            </span>
            @php $pendingCount = \App\Models\Booking::where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span x-show="sidebarOpen" class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                    style="background:#22c55e; color:#052e16;">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        {{-- Laundry --}}
        <a href="{{ route('laundry.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                  {{ request()->routeIs('laundry.orders.*') ? 'text-green-200' : 'hover:text-green-100' }}"
            style="{{ request()->routeIs('laundry.orders.*') ? 'background-color:#166534;' : '' }}"
            onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
            onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
            <i class="ti ti-wash flex-shrink-0 text-lg"
                style="color:{{ request()->routeIs('laundry.orders.*') ? '#86efac' : '#4b7a4b' }};"></i>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                Laundry
            </span>
            @php $laundryPending = \App\Models\LaundryOrder::where('status', 'pending')->count(); @endphp
            @if($laundryPending > 0)
                <span x-show="sidebarOpen" class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                    style="background:#22c55e; color:#052e16;">
                    {{ $laundryPending }}
                </span>
            @endif
        </a>
        @endrole

        {{-- ===== ADMIN FNB ===== --}}
        @role('admin_fnb|super_admin')
        <div x-show="sidebarOpen" class="px-2 pt-4 pb-1">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#2d5a2d;">F&amp;B</p>
        </div>

        {{-- Pesanan Masuk --}}
        <a href="{{ route('fnb.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                  {{ request()->routeIs('fnb.orders.*') ? 'text-green-200' : 'hover:text-green-100' }}"
            style="{{ request()->routeIs('fnb.orders.*') ? 'background-color:#166534;' : '' }}"
            onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
            onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
            <i class="ti ti-clipboard-list flex-shrink-0 text-lg"
                style="color:{{ request()->routeIs('fnb.orders.*') ? '#86efac' : '#4b7a4b' }};"></i>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                Pesanan F&amp;B
            </span>
            @php $fnbPending = \App\Models\BookingItem::where('status', 'pending')->count(); @endphp
            @if($fnbPending > 0)
                <span x-show="sidebarOpen" class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                    style="background:#22c55e; color:#052e16;">
                    {{ $fnbPending }}
                </span>
            @endif
        </a>

        {{-- Menu F&B --}}
        <a href="{{ route('fnb.items.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150
                  {{ request()->routeIs('fnb.items.*') ? 'text-green-200' : 'hover:text-green-100' }}"
            style="{{ request()->routeIs('fnb.items.*') ? 'background-color:#166534;' : '' }}"
            onmouseover="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='rgba(255,255,255,0.05)'"
            onmouseout="if(this.style.backgroundColor !== 'rgb(22, 101, 52)') this.style.backgroundColor='transparent'">
            <i class="ti ti-tools-kitchen-2 flex-shrink-0 text-lg"
                style="color:{{ request()->routeIs('fnb.items.*') ? '#86efac' : '#4b7a4b' }};"></i>
            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">
                Menu F&amp;B
            </span>
        </a>
        @endrole

    </nav>

    {{-- User info --}}
    <div class="flex items-center gap-3 px-3 py-4 flex-shrink-0" style="border-top:0.5px solid rgba(255,255,255,0.08);">
        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                    font-semibold text-sm" style="background-color:#166534; color:#4ade80;">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div x-show="sidebarOpen" class="min-w-0" x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p class="text-white text-xs font-medium truncate">
                {{ auth()->user()->name }}
            </p>
            <p class="text-xs truncate" style="color:#4ade80;">
                {{ auth()->user()->getRoleNames()->first() }}
            </p>
        </div>
    </div>

</aside>
