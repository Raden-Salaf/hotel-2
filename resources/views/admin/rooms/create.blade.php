@extends('layouts.app')

@section('title', 'Tambah Kamar Baru')

@section('content')

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="hover:text-green-600 transition">Kamar</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Tambah Kamar</span>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            {{-- Header form --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Tambah Kamar Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">Isi semua informasi kamar dengan lengkap</p>
            </div>

            {{-- Form --}}
            {{-- enctype="multipart/form-data" WAJIB ada jika form punya upload file --}}
            <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-5">
                @csrf

                {{-- Baris 1: Kategori + Nomor Kamar --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Kategori Kamar <span class="text-red-400">*</span>
                        </label>
                        <select name="room_category_id"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-800 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                   @error('room_category_id') border-red-400 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('room_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_category_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Nomor Kamar <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}"
                            placeholder="Contoh: 101, 202A"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @error('room_number') border-red-400 @enderror">
                        @error('room_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Nama kamar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nama Kamar <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Deluxe King Room"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                              @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsikan kamar ini..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 resize-none">{{ old('description') }}</textarea>
                </div>

                {{-- Baris: Harga + Kapasitas + Lantai --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Harga/Malam (Rp) <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="price_per_night" value="{{ old('price_per_night') }}"
                            placeholder="350000" min="0"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @error('price_per_night') border-red-400 @enderror">
                        @error('price_per_night')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Kapasitas (orang) <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', 2) }}" min="1"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lantai</label>
                        <input type="text" name="floor" value="{{ old('floor') }}" placeholder="1, 2, 3..."
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                    </div>
                </div>

                {{-- Status kamar dengan Alpine.js untuk highlight aktif --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status Kamar <span class="text-red-400">*</span>
                    </label>

                    {{-- x-data menyimpan status yang dipilih saat ini --}}
                    <div x-data="{ selected: '{{ old('status', 'available') }}' }" class="flex items-center gap-3">

                        {{-- Available --}}
                        <label class="flex items-center gap-2 px-4 py-2.5 border-2 rounded-xl cursor-pointer transition"
                            :class="selected === 'available'
                                ?
                                'border-green-500 bg-green-50 text-green-700' :
                                'border-gray-200 text-gray-500 hover:border-gray-300'">
                            <input type="radio" name="status" value="available" x-model="selected" class="hidden">
                            <div class="w-2 h-2 rounded-full"
                                :class="selected === 'available' ? 'bg-green-500' : 'bg-gray-300'"></div>
                            <span class="text-sm font-medium">Tersedia</span>
                        </label>

                        {{-- Occupied --}}
                        <label class="flex items-center gap-2 px-4 py-2.5 border-2 rounded-xl cursor-pointer transition"
                            :class="selected === 'occupied'
                                ?
                                'border-amber-500 bg-amber-50 text-amber-700' :
                                'border-gray-200 text-gray-500 hover:border-gray-300'">
                            <input type="radio" name="status" value="occupied" x-model="selected" class="hidden">
                            <div class="w-2 h-2 rounded-full"
                                :class="selected === 'occupied' ? 'bg-amber-500' : 'bg-gray-300'"></div>
                            <span class="text-sm font-medium">Terisi</span>
                        </label>

                        {{-- Maintenance --}}
                        <label class="flex items-center gap-2 px-4 py-2.5 border-2 rounded-xl cursor-pointer transition"
                            :class="selected === 'maintenance'
                                ?
                                'border-red-500 bg-red-50 text-red-700' :
                                'border-gray-200 text-gray-500 hover:border-gray-300'">
                            <input type="radio" name="status" value="maintenance" x-model="selected" class="hidden">
                            <div class="w-2 h-2 rounded-full"
                                :class="selected === 'maintenance' ? 'bg-red-500' : 'bg-gray-300'"></div>
                            <span class="text-sm font-medium">Maintenance</span>
                        </label>

                    </div>
                </div>

                {{-- Fasilitas (checkbox) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Fasilitas</label>
                    @php
                        $fasilitasList = [
                            'AC',
                            'TV',
                            'WiFi',
                            'Smart TV',
                            'Mini Bar',
                            'Mini Fridge',
                            'Kamar Mandi Dalam',
                            'Bathtub',
                            'Jacuzzi',
                            'Balkon',
                            'Ruang Tamu',
                            'Dapur Kecil',
                            'Sofa',
                            'Safe Box',
                        ];
                        $selectedFasilitas = old('facilities', []);
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach ($fasilitasList as $fasilitas)
                            <label
                                class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg cursor-pointer hover:border-green-400 transition text-xs text-gray-600
                                      {{ in_array($fasilitas, $selectedFasilitas) ? 'border-green-500 bg-green-50 text-green-700' : '' }}">
                                <input type="checkbox" name="facilities[]" value="{{ $fasilitas }}"
                                    {{ in_array($fasilitas, $selectedFasilitas) ? 'checked' : '' }} class="w-3.5 h-3.5"
                                    style="accent-color: #16a34a;">
                                {{ $fasilitas }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Upload Foto Kamar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto Kamar</label>

                    <div x-data="{
                        preview: '{{ isset($room) && $room->image ? Storage::url($room->image) : '' }}',
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

                        {{-- Container foto — pakai padding-top trick untuk rasio 16:9 yang konsisten --}}
                        {{-- padding-top: 56.25% = 9/16 * 100 = rasio 16:9 --}}
                        <div class="relative w-full rounded-xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-green-400 transition cursor-pointer bg-gray-50"
                            style="padding-top: 56.25%;" @click="$refs.fotoInput.click()">

                            {{-- Area konten di dalam — absolute agar ikut tinggi container --}}
                            <div class="absolute inset-0">

                                {{-- Tampil gambar kalau sudah ada preview --}}
                                <template x-if="preview">
                                    <div class="relative w-full h-full">
                                        {{-- Gambar di-crop mengisi penuh container --}}
                                        <img :src="preview" class="w-full h-full object-cover object-center">

                                        {{-- Overlay hover "ganti foto" --}}
                                        <div
                                            class="absolute inset-0 bg-black opacity-0 hover:opacity-40 transition-opacity flex items-center justify-center">
                                            <div
                                                class="text-white text-sm font-medium flex items-center gap-2 opacity-0 hover:opacity-100">
                                                <i class="ti ti-refresh"></i> Ganti Foto
                                            </div>
                                        </div>

                                        {{-- Badge nama file di pojok kiri bawah --}}
                                        <div class="absolute bottom-2 left-2">
                                            <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-lg"
                                                x-text="fileName || 'Foto tersimpan'"></span>
                                        </div>

                                        {{-- Tombol ganti foto di pojok kanan atas --}}
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="bg-white text-gray-600 text-xs px-2.5 py-1.5 rounded-lg shadow flex items-center gap-1 font-medium">
                                                <i class="ti ti-pencil text-sm"></i> Ganti
                                            </span>
                                        </div>
                                    </div>
                                </template>

                                {{-- Placeholder kalau belum ada foto --}}
                                <template x-if="!preview">
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-1"
                                            style="background-color: #f0fdf4;">
                                            <i class="ti ti-photo-up text-3xl" style="color: #86efac;"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Klik untuk pilih foto kamar</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, WEBP — semua ukuran gambar diterima</p>
                                        <p class="text-xs text-gray-300">Gambar akan otomatis di-crop ke rasio 16:9</p>
                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Input file — disembunyikan, dipanggil saat klik container --}}
                        <input type="file" name="image" accept="image/*" x-ref="fotoInput" class="hidden"
                            @change="handleFile($event)">

                        {{-- Info dan nama file terpilih --}}
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-gray-400 flex items-center gap-1">
                                <i class="ti ti-info-circle"></i>
                                Gunakan foto landscape untuk hasil terbaik
                            </p>
                            <p x-show="fileName" x-text="'📎 ' + fileName" class="text-xs text-green-600 font-medium">
                            </p>
                        </div>

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
                        Simpan Kamar
                    </button>
                    <a href="{{ route('admin.rooms.index') }}"
                        class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection
