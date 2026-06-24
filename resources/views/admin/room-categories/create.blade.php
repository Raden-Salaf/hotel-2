@extends('layouts.app')

@section('title', 'Tambah Kategori Kamar')

@section('content')

<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.room-categories.index') }}" class="hover:text-green-600 transition">Kategori Kamar</a>
    <i class="ti ti-chevron-right text-xs"></i>
    <span class="text-gray-600 font-medium">Tambah Kategori</span>
</div>

<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Tambah Kategori Kamar</h2>
            <p class="text-sm text-gray-400 mt-0.5">Contoh: Standard, Deluxe, Suite, Villa</p>
        </div>

        <form action="{{ route('admin.room-categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Nama kategori --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Nama Kategori <span class="text-red-400">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="Contoh: Deluxe, Suite, Standard"
                       autofocus
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                              @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Deskripsi
                    <span class="font-normal text-gray-400">(opsional)</span>
                </label>
                <textarea name="description"
                          rows="4"
                          placeholder="Deskripsikan tipe kamar ini..."
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                                 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                 resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:-translate-y-0.5"
                        style="background-color: #16a34a;">
                    <i class="ti ti-device-floppy mr-1.5"></i>
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.room-categories.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection