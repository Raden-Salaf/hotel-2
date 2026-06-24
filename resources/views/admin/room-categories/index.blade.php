@extends('layouts.app')

@section('title', 'Kategori Kamar')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Kategori Kamar</h2>
        <p class="text-sm text-gray-400 mt-0.5">Kelola tipe-tipe kamar yang tersedia</p>
    </div>
    <a href="{{ route('admin.room-categories.create') }}"
       class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
       style="background-color: #16a34a;">
        <i class="ti ti-plus"></i>
        Tambah Kategori
    </a>
</div>

{{-- Grid kategori --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @forelse($categories as $category)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition">

        {{-- Header card --}}
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                 style="background-color: #f0fdf4;">
                <i class="ti ti-tag text-xl" style="color: #16a34a;"></i>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-1">
                <a href="{{ route('admin.room-categories.edit', $category) }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                   title="Edit">
                    <i class="ti ti-edit text-sm"></i>
                </a>
                <form action="{{ route('admin.room-categories.destroy', $category) }}"
                      method="POST"
                      id="del-cat-{{ $category->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            onclick="confirmDeleteCategory({{ $category->id }}, '{{ $category->name }}', {{ $category->rooms_count }})"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                            title="Hapus">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Info kategori --}}
        <h3 class="font-bold text-gray-800 mb-1">{{ $category->name }}</h3>
        <p class="text-xs text-gray-400 mb-4 leading-relaxed">
            {{ $category->description ?? 'Tidak ada deskripsi' }}
        </p>

        {{-- Badge jumlah kamar --}}
        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
            <i class="ti ti-building-estate text-sm" style="color: #16a34a;"></i>
            <span class="text-xs text-gray-500">
                <span class="font-semibold text-gray-700">{{ $category->rooms_count }}</span>
                kamar terdaftar
            </span>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-16 text-center">
        <i class="ti ti-tag-off text-5xl text-gray-200 block mb-3"></i>
        <p class="text-gray-400">Belum ada kategori kamar</p>
        <a href="{{ route('admin.room-categories.create') }}"
           class="inline-block mt-3 text-sm font-medium"
           style="color: #16a34a;">
            + Tambah kategori pertama
        </a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($categories->hasPages())
    <div>{{ $categories->links() }}</div>
@endif

@endsection

@push('scripts')
<script>
    /**
     * Konfirmasi hapus kategori
     * Jika kategori masih punya kamar, tampilkan peringatan berbeda
     */
    function confirmDeleteCategory(id, name, roomCount) {
        // Jika masih ada kamar, langsung tolak dengan info
        if (roomCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Bisa Dihapus!',
                html: `Kategori <strong>"${name}"</strong> masih memiliki <strong>${roomCount} kamar</strong>.<br>Hapus atau pindahkan kamarnya terlebih dahulu.`,
                confirmButtonColor: '#16a34a',
                confirmButtonText: 'Mengerti',
            })
            return
        }

        // Jika tidak ada kamar, tampilkan konfirmasi hapus
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>"${name}"</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="ti ti-trash"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`del-cat-${id}`).submit()
            }
        })
    }
</script>
@endpush