@extends('layouts.app')

@section('title', 'Edit Menu — ' . $fnbItem->name)

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.fnb-items.index') }}" class="hover:text-green-600 transition">Menu F&B</a>
    <i class="ti ti-chevron-right text-xs"></i>
    <span class="text-gray-600 font-medium">Edit {{ $fnbItem->name }}</span>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Edit Menu F&B</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $fnbItem->category->icon ?? '' }} {{ $fnbItem->category->name ?? '' }}
            </p>
        </div>

        {{-- method PUT karena ini request update --}}
        <form action="{{ route('admin.fnb-items.update', $fnbItem) }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Kategori --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Kategori <span class="text-red-400">*</span>
                </label>
                <select name="fnb_category_id"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                               focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                {{-- old() untuk saat validasi gagal, fallback ke data asli --}}
                                {{ old('fnb_category_id', $fnbItem->fnb_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nama menu --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Nama Menu <span class="text-red-400">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $fnbItem->name) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                <textarea name="description"
                          rows="3"
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                                 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                 resize-none">{{ old('description', $fnbItem->description) }}</textarea>
            </div>

            {{-- Harga + Status --}}
            <div class="grid grid-cols-2 gap-4">

                {{-- Harga --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Harga (Rp) <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           name="price"
                           value="{{ old('price', $fnbItem->price) }}"
                           min="0"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                                  focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @error('price') border-red-400 @enderror">
                    @error('price')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Toggle ketersediaan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status Ketersediaan
                    </label>
                    {{-- Nilai awal: cek old() dulu, kalau tidak ada pakai data dari DB --}}
                    <label class="flex items-center gap-3 px-4 py-2.5 border-2 rounded-xl cursor-pointer transition"
                           x-data="{ on: {{ old('is_available', $fnbItem->is_available) ? 'true' : 'false' }} }"
                           :class="on ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white'">

                        <input type="hidden" name="is_available" :value="on ? '1' : '0'">

                        <div class="relative w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer flex-shrink-0"
                             :class="on ? 'bg-green-500' : 'bg-gray-300'"
                             @click="on = !on">
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
                                 :class="on ? 'translate-x-5' : 'translate-x-0'">
                            </div>
                        </div>

                        <span class="text-sm font-medium transition-colors"
                              :class="on ? 'text-green-700' : 'text-gray-400'"
                              x-text="on ? 'Tersedia' : 'Tidak Tersedia'">
                        </span>
                    </label>
                </div>
            </div>

            {{-- Upload foto dengan preview foto lama --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto Menu</label>

                {{-- Nilai awal preview = foto yang sudah ada di DB --}}
                {{-- Kalau belum ada foto, preview = string kosong --}}
                <div x-data="{
                        preview: '{{ $fnbItem->image ? Storage::url($fnbItem->image) : '' }}',
                        fileName: '',
                        handleFile(event) {
                            const file = event.target.files[0]
                            if (!file) return
                            this.fileName = file.name
                            const reader = new FileReader()
                            reader.onload = (e) => { this.preview = e.target.result }
                            reader.readAsDataURL(file)
                        }
                    }">

                    <div class="relative w-full rounded-xl overflow-hidden border-2 border-dashed border-gray-200
                                hover:border-green-400 transition cursor-pointer"
                         style="padding-top: 75%; background-color: #f8fafc;"
                         @click="$refs.fotoInputEdit.click()">

                        <div class="absolute inset-0 flex items-center justify-center p-3">

                            {{-- Preview foto (foto lama atau foto baru yang baru dipilih) --}}
                            <template x-if="preview">
                                <div class="relative w-full h-full flex items-center justify-center">
                                    <img :src="preview"
                                         class="max-h-full max-w-full object-contain"
                                         style="border-radius: 8px;">
                                    <div class="absolute top-0 right-0">
                                        <span class="bg-white text-gray-600 text-xs px-2.5 py-1.5 rounded-lg shadow
                                                     flex items-center gap-1 font-medium">
                                            <i class="ti ti-pencil text-sm"></i> Ganti
                                        </span>
                                    </div>
                                </div>
                            </template>

                            {{-- Placeholder kalau memang belum ada foto sama sekali --}}
                            <template x-if="!preview">
                                <div class="flex flex-col items-center justify-center gap-2 text-center">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-1"
                                         style="background-color: #f0fdf4;">
                                        <i class="ti ti-photo-up text-3xl" style="color: #86efac;"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Klik untuk ganti foto menu</p>
                                    <p class="text-xs text-gray-400">PNG, JPG, WEBP — semua ukuran diterima</p>
                                </div>
                            </template>

                        </div>
                    </div>

                    {{-- Ref berbeda dari create agar tidak konflik jika keduanya ada di DOM --}}
                    <input type="file"
                           name="image"
                           accept="image/*"
                           x-ref="fotoInputEdit"
                           class="hidden"
                           @change="handleFile($event)">

                    {{-- Info file baru yang dipilih --}}
                    <p x-show="fileName"
                       x-text="'📎 ' + fileName"
                       class="mt-1.5 text-xs font-medium"
                       style="color: #16a34a;">
                    </p>

                </div>

                @error('image')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
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
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.fnb-items.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection