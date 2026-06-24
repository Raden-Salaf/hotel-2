@extends('layouts.app')

@section('title', 'Menu F&B')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Menu F&B</h2>
            <p class="text-sm text-gray-400 mt-0.5">Total {{ $items->total() }} menu terdaftar</p>
        </div>
        <a href="{{ route('admin.fnb-items.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
            style="background-color: #16a34a;">
            <i class="ti ti-plus"></i>
            Tambah Menu
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5 flex items-center gap-3 flex-wrap">
        <span class="text-xs font-semibold text-gray-500">Filter:</span>

        {{-- Filter kategori --}}
        <select
            onchange="window.location.href='{{ route('admin.fnb-items.index') }}?category='+this.value+'&status={{ request('status') }}'"
            class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-green-500">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
            @endforeach
        </select>

        {{-- Filter status --}}
        @foreach (['all' => 'Semua', 'available' => 'Tersedia', 'unavailable' => 'Tidak Tersedia'] as $val => $label)
            <a href="{{ route('admin.fnb-items.index', [
                'category' => request('category'),
                'status' => $val === 'all' ? '' : $val,
            ]) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                  {{ request('status', '') === ($val === 'all' ? '' : $val) ? 'text-white' : 'text-gray-500 hover:bg-gray-100' }}"
                style="{{ request('status', '') === ($val === 'all' ? '' : $val) ? 'background-color:#16a34a;' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Grid menu --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($items as $item)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition">

                {{-- Foto menu — object-contain agar semua ukuran gambar muat --}}
                <div class="relative overflow-hidden" style="padding-top: 75%; background-color: #f8fafc;">
                    <div class="absolute inset-0 flex items-center justify-center p-2">
                        @if ($item->image)
                            {{-- object-contain = gambar mengecil agar muat, tidak terpotong --}}
                            {{-- max-h-full max-w-full = batasi ukuran maksimal gambar --}}
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                                class="max-h-full max-w-full object-contain" style="border-radius: 8px;">
                        @else
                            <div class="flex flex-col items-center justify-center gap-1">
                                <i class="ti ti-tools-kitchen-2 text-4xl" style="color: #bbf7d0;"></i>
                            </div>
                        @endif

                        {{-- Badge status — selalu tampil di pojok kiri atas --}}
                        <div class="absolute top-2 left-2">
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold
                         {{ $item->is_available ? 'bg-green-500 text-white' : 'bg-gray-400 text-white' }}">
                                {{ $item->is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Info + tombol aksi --}}
                <div class="p-3">
                    {{-- Kategori --}}
                    <p class="text-xs text-gray-400 mb-0.5">
                        {{ $item->category->icon ?? '' }} {{ $item->category->name ?? '-' }}
                    </p>

                    {{-- Nama menu --}}
                    <h3 class="font-semibold text-gray-800 text-sm leading-tight mb-1">
                        {{ $item->name }}
                    </h3>

                    {{-- Deskripsi singkat --}}
                    @if ($item->description)
                        <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                            {{ $item->description }}
                        </p>
                    @endif

                    {{-- Harga --}}
                    <p class="font-bold text-sm mb-3" style="color: #16a34a;">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </p>

                    {{-- Tombol aksi — selalu tampil --}}
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                        {{-- Tombol Edit --}}
                        <a href="{{ route('admin.fnb-items.edit', $item) }}"
                            class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                            <i class="ti ti-edit text-sm"></i>
                            Edit
                        </a>

                        {{-- Tombol Hapus --}}
                        {{-- Pakai data-* attribute untuk pass data, bukan onclick langsung --}}
                        <button type="button"
                            class="btn-delete-fnb flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 transition"
                            data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                            <i class="ti ti-trash text-sm"></i>
                            Hapus
                        </button>

                        {{-- Form hapus tersembunyi --}}
                        <form action="{{ route('admin.fnb-items.destroy', $item) }}" method="POST"
                            id="del-fnb-{{ $item->id }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 py-16 text-center">
                <i class="ti ti-tools-kitchen-2 text-5xl text-gray-200 block mb-3"></i>
                <p class="text-gray-400">Belum ada menu F&B</p>
                <a href="{{ route('admin.fnb-items.create') }}" class="inline-block mt-3 text-sm font-medium"
                    style="color: #16a34a;">+ Tambah menu pertama</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($items->hasPages())
        <div class="mt-5">{{ $items->links() }}</div>
    @endif

@endsection

@push('scripts')
    <script>
        // Pakai event delegation — satu listener untuk semua tombol hapus
        // Lebih efisien daripada pasang onclick di tiap tombol
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete-fnb')
            if (!btn) return

            // Ambil data dari attribute, sudah aman karena tidak lewat JS string
            const id = btn.dataset.id
            const name = btn.dataset.name

            Swal.fire({
                title: 'Hapus Menu?',
                html: `Menu <strong>"${name}"</strong> akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`del-fnb-${id}`).submit()
                }
            })
        })
    </script>
@endpush
