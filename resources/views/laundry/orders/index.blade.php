@extends('layouts.app')

@section('title', 'Pesanan Laundry')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pesanan Laundry</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola pesanan laundry dari tamu</p>
        </div>
        <a href="{{ route('laundry.orders.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white
              rounded-xl transition hover:-translate-y-0.5"
            style="background-color:#16a34a;">
            <i class="ti ti-plus"></i>
            Buat Pesanan Baru
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Menunggu Proses</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-yellow-50">
                    <i class="ti ti-clock text-yellow-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Sedang Diproses</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-blue-50">
                    <i class="ti ti-wash text-blue-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Selesai Hari Ini</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-green-50">
                    <i class="ti ti-check text-green-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['done'] }}</p>
        </div>
    </div>

    {{-- Filter status --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5 flex items-center gap-2">
        @foreach ([
            '' => 'Aktif',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'done' => 'Selesai',
        ] as $val => $label)
            <a href="{{ route('laundry.orders.index', $val ? ['status' => $val] : []) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                  {{ request('status', '') === $val ? 'text-white' : 'text-gray-500 hover:bg-gray-100' }}"
                style="{{ request('status', '') === $val ? 'background-color:#16a34a;' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Tabel pesanan --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wide">
                        <th class="text-left px-6 py-3">Item Laundry</th>
                        <th class="text-left px-6 py-3">Tamu / Kamar</th>
                        <th class="text-left px-6 py-3">Qty</th>
                        <th class="text-left px-6 py-3">Subtotal</th>
                        <th class="text-left px-6 py-3">Catatan</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Item laundry --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $order->laundryItem->icon ?? '👕' }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $order->laundryItem->name ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            per {{ $order->laundryItem->unit ?? 'pcs' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Tamu --}}
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-700">
                                    {{ $order->booking->guest_name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    Kamar {{ $order->booking->room->room_number ?? '-' }}
                                </p>
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $order->quantity }}x
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-800">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ $order->notes ?? '-' }}
                            </td>

                            {{-- Badge status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'done' => 'bg-green-100 text-green-700',
                                    ];
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                     {{ $statusConfig[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            {{-- Tombol update status --}}
                            <td class="px-6 py-4">
                                @if ($order->status === 'pending')
                                    <form action="{{ route('laundry.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white
                                               rounded-lg transition"
                                            style="background:#3b82f6;">
                                            <i class="ti ti-wash mr-1"></i> Proses
                                        </button>
                                    </form>
                                @elseif($order->status === 'processing')
                                    <form action="{{ route('laundry.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white
                                               rounded-lg transition"
                                            style="background:#16a34a;">
                                            <i class="ti ti-check mr-1"></i> Selesai
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="text-5xl block mb-3">👕</span>
                                <p class="text-gray-400">Tidak ada pesanan laundry</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

@endsection
