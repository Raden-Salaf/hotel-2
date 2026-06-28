@extends('layouts.app')

@section('title', 'Tambah Item Laundry')

@section('content')

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('admin.laundry.index') }}" class="hover:text-green-600 transition">Item Laundry</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Tambah Item</span>
    </div>

    <div class="max-w-lg">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Tambah Item Laundry</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Contoh: Baju, Celana, Jaket, Sepatu
                </p>
            </div>

            <form action="{{ route('admin.laundry.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Nama item --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nama Item <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Baju, Celana, Jaket" autofocus
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                              bg-gray-50 focus:outline-none focus:border-green-500
                              focus:ring-2 focus:ring-green-100
                              @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">
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
                    <textarea name="description" rows="2" placeholder="Keterangan tambahan..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                 bg-gray-50 focus:outline-none focus:border-green-500
                                 focus:ring-2 focus:ring-green-100 resize-none">{{ old('description') }}</textarea>
                </div>

                {{-- Harga + Satuan --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Harga <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="price" value="{{ old('price') }}" placeholder="5000" min="0"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                  bg-gray-50 focus:outline-none focus:border-green-500
                                  focus:ring-2 focus:ring-green-100
                                  @error('price') border-red-400 @enderror">
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Satuan <span class="text-red-400">*</span>
                        </label>
                        <select name="unit"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                   bg-gray-50 focus:outline-none focus:border-green-500
                                   focus:ring-2 focus:ring-green-100">
                            <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>
                                Per Pcs (satuan)
                            </option>
                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>
                                Per Kg (kilogram)
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Icon emoji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Icon Emoji
                    </label>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                border border-gray-200 bg-gray-50 text-2xl flex-shrink-0"
                            id="icon-preview">
                            {{ old('icon', '👕') }}
                        </div>
                        <input type="text" name="icon" value="{{ old('icon', '👕') }}" placeholder="Emoji icon"
                            maxlength="10"
                            class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                  bg-gray-50 focus:outline-none focus:border-green-500
                                  focus:ring-2 focus:ring-green-100"
                            oninput="document.getElementById('icon-preview').textContent = this.value || '👕'">
                    </div>

                    {{-- Pilihan emoji cepat --}}
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach (['👕', '👖', '🧥', '👗', '👔', '🧤', '🧦', '👙', '🩱', '🩲', '🩳', '👟'] as $emoji)
                            <button type="button"
                                class="w-9 h-9 rounded-xl border border-gray-200 hover:border-green-400
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

                {{-- Status ketersediaan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status Ketersediaan
                    </label>
                    <label
                        class="flex items-center gap-3 px-4 py-2.5 border-2 rounded-xl
                              cursor-pointer transition"
                        x-data="{ on: true }" :class="on ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                        <input type="hidden" name="is_available" :value="on ? '1' : '0'">
                        <div class="relative w-10 h-5 rounded-full transition-colors flex-shrink-0"
                            :class="on ? 'bg-green-500' : 'bg-gray-300'" @click="on = !on">
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full
                                    shadow transition-transform"
                                :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                        </div>
                        <span class="text-sm font-medium" :class="on ? 'text-green-700' : 'text-gray-400'"
                            x-text="on ? 'Tersedia' : 'Tidak Tersedia'"></span>
                    </label>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl
                               transition hover:-translate-y-0.5"
                        style="background-color:#16a34a;">
                        <i class="ti ti-device-floppy mr-1.5"></i>
                        Simpan Item
                    </button>
                    <a href="{{ route('admin.laundry.index') }}"
                        class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100
                          rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
