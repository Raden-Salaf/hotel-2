@extends('layouts.app')

@section('title', 'Manajemen Kamar')

@section('content')

    {{-- Header halaman --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Kamar</h2>
            <p class="text-sm text-gray-400 mt-0.5">Total {{ $rooms->total() }} kamar terdaftar</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
            style="background-color: #16a34a;">
            <i class="ti ti-plus text-base"></i>
            Tambah Kamar
        </a>
    </div>

    {{-- Tabel daftar kamar --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Header tabel dengan filter status --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Kamar</h3>

            {{-- Filter status kamar --}}
            <div class="flex items-center gap-2">
                @foreach (['all' => 'Semua', 'available' => 'Tersedia', 'occupied' => 'Terisi', 'maintenance' => 'Maintenance'] as $value => $label)
                    <a href="{{ $value === 'all' ? route('admin.rooms.index') : route('admin.rooms.index', ['status' => $value]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                          {{ request('status', 'all') === $value ? 'text-white' : 'text-gray-500 hover:bg-gray-100' }}"
                        style="{{ request('status', 'all') === $value ? 'background-color: #16a34a;' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wide">
                        <th class="text-left px-6 py-3">Kamar</th>
                        <th class="text-left px-6 py-3">Kategori</th>
                        <th class="text-left px-6 py-3">Lantai</th>
                        <th class="text-left px-6 py-3">Kapasitas</th>
                        <th class="text-left px-6 py-3">Harga/Malam</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Foto + nama kamar --}}
                            <td class="px-6 py-4">
                                {{-- Foto kamar dengan crop otomatis --}}
                                {{-- w-14 h-14 = ukuran container fixed, overflow-hidden = potong yang keluar --}}
                                <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                    @if ($room->image)
                                        {{-- object-cover = gambar di-scale & crop agar mengisi penuh container --}}
                                        {{-- w-full h-full = gambar mengisi 100% container --}}
                                        <img src="{{ Storage::url($room->image) }}" alt="{{ $room->name }}"
                                            class="w-full h-full object-cover object-center">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="ti ti-building-estate text-gray-300 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $room->name }}</p>
                                    <p class="text-xs text-gray-400">No. {{ $room->room_number }}</p>
                                </div>
        </div>
        </td>

        <td class="px-6 py-4 text-gray-600">{{ $room->category->name ?? '-' }}</td>
        <td class="px-6 py-4 text-gray-600">Lantai {{ $room->floor ?? '-' }}</td>
        <td class="px-6 py-4 text-gray-600">{{ $room->capacity }} orang</td>

        {{-- Format harga dengan rupiah --}}
        <td class="px-6 py-4 font-semibold text-gray-800">
            Rp {{ number_format($room->price_per_night, 0, ',', '.') }}
        </td>

        {{-- Badge status dengan warna berbeda --}}
        <td class="px-6 py-4">
            @php
                $statusConfig = [
                    'available' => ['bg-green-100 text-green-700', 'Tersedia'],
                    'occupied' => ['bg-amber-100 text-amber-700', 'Terisi'],
                    'maintenance' => ['bg-red-100 text-red-700', 'Maintenance'],
                ];
                [$badgeClass, $badgeLabel] = $statusConfig[$room->status] ?? [
                    'bg-gray-100 text-gray-600',
                    $room->status,
                ];
            @endphp
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                {{ $badgeLabel }}
            </span>
        </td>

        {{-- Tombol aksi --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                {{-- Tombol edit --}}
                <a href="{{ route('admin.rooms.edit', $room) }}"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                    title="Edit">
                    <i class="ti ti-edit text-sm"></i>
                </a>

                {{-- Tombol hapus dengan konfirmasi SweetAlert --}}
                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST"
                    id="delete-form-{{ $room->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete({{ $room->id }}, '{{ $room->name }}')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                        title="Hapus">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </form>
            </div>
        </td>
        </tr>
    @empty
        {{-- Tampil kalau data kosong --}}
        <tr>
            <td colspan="7" class="px-6 py-16 text-center">
                <i class="ti ti-building-off text-4xl text-gray-200 block mb-3"></i>
                <p class="text-gray-400 text-sm">Belum ada kamar terdaftar</p>
                <a href="{{ route('admin.rooms.create') }}" class="inline-block mt-3 text-xs font-medium"
                    style="color: #16a34a;">
                    + Tambah kamar pertama
                </a>
            </td>
        </tr>
        @endforelse
        </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($rooms->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $rooms->links() }}
        </div>
    @endif

    </div>

    @push('scripts')
        <script>
            /**
             * Fungsi konfirmasi hapus dengan SweetAlert
             * @param {number} id    - ID kamar yang mau dihapus
             * @param {string} name  - Nama kamar untuk ditampilkan di dialog
             */
            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Hapus Kamar?',
                    text: `Kamar "${name}" akan dihapus permanen dan tidak bisa dikembalikan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626', // merah untuk hapus
                    cancelButtonColor: '#6b7280', // abu untuk batal
                    confirmButtonText: '<i class="ti ti-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true, // tombol batal di kiri
                }).then((result) => {
                    // Jika user klik "Ya, Hapus!" → submit form
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit()
                    }
                })
            }
        </script>
    @endpush

@endsection
