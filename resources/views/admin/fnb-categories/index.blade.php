@extends('layouts.app')

@section('title', 'Tambah Kategori F&B')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.fnb-categories.index') }}"
       class="hover:text-green-600 transition">
        Kategori F&B
    </a>
    <i class="ti ti-chevron-right text-xs"></i>
    <span class="text-gray-600 font-medium">Tambah Kategori</span>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Tambah Kategori F&B</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                Contoh: Makanan, Minuman, Dessert, Snack
            </p>
        </div>

        <form action="{{ route('admin.fnb-categories.store') }}"
              method="POST"
              class="p-6 space-y-5">
            @csrf

            {{-- Nama kategori --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Nama Kategori <span class="text-red-400">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="Contoh: Makanan, Minuman, Dessert"
                       autofocus
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                              bg-gray-50 focus:outline-none focus:border-green-500
                              focus:ring-2 focus:ring-green-100
                              @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Icon emoji --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Icon Emoji
                    <span class="font-normal text-gray-400">(opsional)</span>
                </label>
                <div class="flex items-center gap-3">
                    {{-- Preview icon --}}
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                                border border-gray-200 bg-gray-50 text-2xl"
                         id="icon-preview">
                        {{ old('icon', '🍽️') }}
                    </div>
                    <input type="text"
                           name="icon"
                           value="{{ old('icon', '🍽️') }}"
                           placeholder="Contoh: 🍽️ 🥤 🍰 🍟"
                           maxlength="10"
                           class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                  bg-gray-50 focus:outline-none focus:border-green-500
                                  focus:ring-2 focus:ring-green-100"
                           oninput="document.getElementById('icon-preview').textContent = this.value || '🍽️'">
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    Masukkan satu emoji sebagai icon kategori
                </p>
                @error('icon')
                    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Pilihan emoji cepat --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-2">Pilihan Cepat:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['🍽️','🥤','🍰','🍟','🍜','🥗','🍕','🍣','☕','🧃','🍦','🥘'] as $emoji)
                        <button type="button"
                                class="w-10 h-10 rounded-xl border border-gray-200 hover:border-green-400
                                       hover:bg-green-50 transition text-xl flex items-center justify-center"
                                onclick="
                                    document.querySelector('[name=icon]').value = '{{ $emoji }}';
                                    document.getElementById('icon-preview').textContent = '{{ $emoji }}';
                                ">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl
                               transition hover:-translate-y-0.5"
                        style="background-color:#16a34a;">
                    <i class="ti ti-device-floppy mr-1.5"></i>
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.fnb-categories.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100
                          rounded-xl hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection