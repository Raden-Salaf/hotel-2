@extends('layouts.app')

@section('title', 'Manajemen Booking')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Booking</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua pemesanan kamar hotel</p>
        </div>
        <a href="{{ route('resepsionis.bookings.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
            style="background-color: #16a34a;">
            <i class="ti ti-plus"></i>
            Booking Walk-in
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @foreach ([['label' => 'Pending', 'value' => $stats['pending'], 'color' => 'bg-yellow-50 text-yellow-600', 'icon' => 'ti-clock'], ['label' => 'Confirmed', 'value' => $stats['confirmed'], 'color' => 'bg-blue-50 text-blue-600', 'icon' => 'ti-circle-check'], ['label' => 'Check-in', 'value' => $stats['checked_in'], 'color' => 'bg-green-50 text-green-600', 'icon' => 'ti-login'], ['label' => 'Tiba Hari Ini', 'value' => $stats['today_in'], 'color' => 'bg-purple-50 text-purple-600', 'icon' => 'ti-calendar-down'], ['label' => 'Keluar Hari Ini', 'value' => $stats['today_out'], 'color' => 'bg-orange-50 text-orange-600', 'icon' => 'ti-calendar-up']] as $stat)
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $stat['color'] }}">
                        <i class="ti {{ $stat['icon'] }} text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5 flex items-center gap-3 flex-wrap">

        {{-- Search --}}
        <form method="GET" action="{{ route('resepsionis.bookings.index') }}"
            class="flex items-center gap-3 flex-1 flex-wrap">

            <div class="relative flex-1 min-w-48">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode booking / nama tamu..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50
                          focus:outline-none focus:border-green-500">
            </div>

            {{-- Filter status --}}
            <select name="status"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50
                       focus:outline-none focus:border-green-500">
                <option value="">Semua Status</option>
                @foreach (['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>

            {{-- Filter tipe --}}
            <select name="type"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50
                       focus:outline-none focus:border-green-500">
                <option value="">Semua Tipe</option>
                <option value="online" {{ request('type') === 'online' ? 'selected' : '' }}>Online</option>
                <option value="walk_in" {{ request('type') === 'walk_in' ? 'selected' : '' }}>Walk-in</option>
            </select>

            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-xl"
                style="background:#16a34a;">
                <i class="ti ti-filter mr-1"></i> Filter
            </button>

            @if (request()->hasAny(['search', 'status', 'type']))
                <a href="{{ route('resepsionis.bookings.index') }}"
                    class="px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel booking --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wide">
                        <th class="text-left px-6 py-3">Kode Booking</th>
                        <th class="text-left px-6 py-3">Tamu</th>
                        <th class="text-left px-6 py-3">Kamar</th>
                        <th class="text-left px-6 py-3">Check-in</th>
                        <th class="text-left px-6 py-3">Check-out</th>
                        <th class="text-left px-6 py-3">Total</th>
                        <th class="text-left px-6 py-3">Tipe</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Kode booking --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('resepsionis.bookings.show', $booking) }}"
                                    class="font-mono font-bold text-sm hover:underline" style="color:#16a34a;">
                                    {{ $booking->booking_code }}
                                </a>
                            </td>

                            {{-- Tamu --}}
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800">{{ $booking->guest_name }}</p>
                                <p class="text-xs text-gray-400">{{ $booking->guest_phone }}</p>
                            </td>

                            {{-- Kamar --}}
                            <td class="px-6 py-4 text-gray-600">
                                {{ $booking->room->name ?? '-' }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>

                            {{-- Tipe booking --}}
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                     {{ $booking->booking_type === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $booking->booking_type === 'online' ? 'Online' : 'Walk-in' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        'checked_in' => 'bg-green-100 text-green-700',
                                        'checked_out' => 'bg-gray-100 text-gray-600',
                                        'cancelled' => 'bg-red-100 text-red-600',
                                    ];
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                     {{ $statusConfig[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    {{-- Detail --}}
                                    <a href="{{ route('resepsionis.bookings.show', $booking) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50
                                      text-gray-500 hover:bg-gray-100 transition"
                                        title="Detail">
                                        <i class="ti ti-eye text-sm"></i>
                                    </a>

                                    {{-- Konfirmasi (hanya untuk pending) --}}
                                    @if ($booking->status === 'pending')
                                        <form action="{{ route('resepsionis.bookings.confirm', $booking) }}" method="POST"
                                            id="confirm-{{ $booking->id }}">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50
                                                   text-blue-600 hover:bg-blue-100 transition"
                                                data-id="{{ $booking->id }}" data-code="{{ $booking->booking_code }}"
                                                title="Konfirmasi"
                                                onclick="confirmBooking(this.dataset.id, this.dataset.code)">
                                                <i class="ti ti-circle-check text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Check-in (hanya confirmed) --}}
                                    @if ($booking->status === 'confirmed')
                                        <form action="{{ route('resepsionis.bookings.check-in', $booking) }}"
                                            method="POST" id="checkin-{{ $booking->id }}">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50
                                                   text-green-600 hover:bg-green-100 transition"
                                                data-id="{{ $booking->id }}" data-name="{{ $booking->guest_name }}"
                                                title="Check-in" onclick="doCheckIn(this.dataset.id, this.dataset.name)">
                                                <i class="ti ti-login text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Check-out (hanya checked_in) --}}
                                    @if ($booking->status === 'checked_in')
                                        <form action="{{ route('resepsionis.bookings.check-out', $booking) }}"
                                            method="POST" id="checkout-{{ $booking->id }}">
                                            @csrf @method('PATCH')
                                            <button type="button"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50
                                                   text-orange-600 hover:bg-orange-100 transition"
                                                data-id="{{ $booking->id }}" data-name="{{ $booking->guest_name }}"
                                                title="Check-out"
                                                onclick="doCheckOut(this.dataset.id, this.dataset.name)">
                                                <i class="ti ti-logout text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Invoice --}}
                                    @if (!in_array($booking->status, ['pending', 'cancelled']))
                                        <a href="{{ route('resepsionis.bookings.invoice', $booking) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50
                                          text-purple-600 hover:bg-purple-100 transition"
                                            title="Invoice">
                                            <i class="ti ti-receipt text-sm"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <i class="ti ti-calendar-off text-5xl text-gray-200 block mb-3"></i>
                                <p class="text-gray-400">Belum ada booking</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        // Konfirmasi booking online → confirmed
        function confirmBooking(id, code) {
            Swal.fire({
                title: 'Konfirmasi Booking?',
                html: `Booking <strong>${code}</strong> akan dikonfirmasi.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Konfirmasi!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`confirm-${id}`).submit()
                }
            })
        }

        // Check-in tamu
        function doCheckIn(id, name) {
            Swal.fire({
                title: 'Check-in Tamu?',
                html: `Tamu <strong>${name}</strong> akan di-check-in sekarang.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Check-in!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`checkin-${id}`).submit()
                }
            })
        }

        // Check-out tamu
        function doCheckOut(id, name) {
            Swal.fire({
                title: 'Check-out Tamu?',
                html: `Tamu <strong>${name}</strong> akan di-check-out sekarang.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Check-out!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`checkout-${id}`).submit()
                }
            })
        }
    </script>
@endpush
