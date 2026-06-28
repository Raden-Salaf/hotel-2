@extends('layouts.app')

@section('title', 'Item Laundry')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Item Laundry</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola daftar item & tarif laundry</p>
        </div>
        <a href="{{ route('admin.laundry.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white
              rounded-xl transition hover:-translate-y-0.5"
            style="background-color:#16a34a;">
            <i class="ti ti-plus"></i>
            Tambah Item
        </a>
    </div>

    {{-- Grid item --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition">

                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                            style="background:#f0fdf4;">
                            <span class="text-2xl">{{ $item->icon ?? '👕' }}</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                per {{ $item->unit === 'kg' ? 'Kilogram' : 'Pcs' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="{{ route('admin.laundry.edit', $item) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                          bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                            <i class="ti ti-edit text-sm"></i>
                        </a>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                               bg-red-50 text-red-500 hover:bg-red-100 transition"
                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                            onclick="confirmDeleteLaundry(this.dataset.id, this.dataset.name)">
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                        <form action="{{ route('admin.laundry.destroy', $item) }}" method="POST"
                            id="del-laundry-{{ $item->id }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                {{-- Deskripsi --}}
                @if ($item->description)
                    <p class="text-xs text-gray-400 mb-3">{{ $item->description }}</p>
                @endif

                {{-- Harga & Status --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div>
                        <p class="text-lg font-bold" style="color:#16a34a;">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400">per {{ $item->unit }}</p>
                    </div>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-semibold
                         {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $item->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-16 text-center">
                <span class="text-6xl block mb-3">👕</span>
                <p class="text-gray-400">Belum ada item laundry</p>
                <a href="{{ route('admin.laundry.create') }}" class="inline-block mt-3 text-sm font-medium"
                    style="color:#16a34a;">
                    + Tambah item pertama
                </a>
            </div>
        @endforelse
    </div>

    @if ($items->hasPages())
        <div class="mt-5">{{ $items->links() }}</div>
    @endif

@endsection

@push('scripts')
    <script>
        function confirmDeleteLaundry(id, name) {
            Swal.fire({
                title: 'Hapus Item?',
                html: `Item laundry <strong>"${name}"</strong> akan dihapus!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`del-laundry-${id}`).submit()
                }
            })
        }
    </script>
@endpush
