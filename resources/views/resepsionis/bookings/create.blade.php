@extends('layouts.app')

@section('title', 'Booking Walk-in')

@section('content')

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('resepsionis.bookings.index') }}" class="hover:text-green-600 transition">Booking</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Booking Walk-in</span>
    </div>

    <div class="max-w-4xl" x-data="{
                         selectedRoom: null,
                         checkIn: '',
                         checkOut: '',
                         fnbItems: {},

                         get nights() {
                             if (!this.checkIn || !this.checkOut) return 0
                             const diff = Math.floor(
                                 (new Date(this.checkOut) - new Date(this.checkIn)) / 86400000
                             )
                             return diff > 0 ? diff : 0
                         },

                         get roomTotal() {
                             if (!this.selectedRoom) return 0
                             return this.nights * this.selectedRoom.price
                         },

                         get fnbTotal() {
                             let total = 0
                             Object.values(this.fnbItems).forEach(i => total += i.price * i.qty)
                             return total
                         },

                         get grandTotal() {
                             return (this.roomTotal + this.fnbTotal) * 1.11
                         },

                         rupiah(num) {
                             return 'Rp ' + Math.round(num).toLocaleString('id-ID')
                         },

                         setFnb(id, price, name, qty) {
                             if (qty <= 0) {
                                 delete this.fnbItems[id]
                                 this.fnbItems = { ...this.fnbItems }
                             } else {
                                 this.fnbItems = {
                                     ...this.fnbItems,
                                     [id]: { price, name, qty }
                                 }
                             }
                         }
                     }" @fnb-update.window="setFnb(
                         $event.detail.id,
                         $event.detail.price,
                         $event.detail.name,
                         $event.detail.qty
                     )">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ==========================================
            KOLOM KIRI — Form
            ========================================== --}}
            <div class="lg:col-span-2 space-y-5">

                <form action="{{ route('resepsionis.bookings.store') }}" method="POST" id="walkin-form">
                    @csrf

                    {{-- Pilih Kamar --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ti ti-building-estate" style="color:#16a34a;"></i>
                            Pilih Kamar
                        </h3>

                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            @foreach($rooms as $room)
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2
                                                                      cursor-pointer transition" :class="selectedRoom && selectedRoom.id === {{ $room->id }}
                                                                   ? 'border-green-500 bg-green-50'
                                                                   : 'border-gray-100 hover:border-gray-200'" @click="selectedRoom = {
                                                                   id: {{ $room->id }},
                                                                   price: {{ $room->price_per_night }},
                                                                   name: '{{ $room->name }}'
                                                               }">
                                    <input type="radio" name="room_id" value="{{ $room->id }}" class="hidden">

                                    {{-- Foto kamar --}}
                                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0
                                                                        bg-gray-100 flex items-center justify-center">
                                        @if($room->image)
                                            <img src="{{ Storage::url($room->image) }}" class="w-full h-full object-cover"
                                                alt="{{ $room->name }}">
                                        @else
                                            <i class="ti ti-building-estate text-gray-300 text-2xl"></i>
                                        @endif
                                    </div>

                                    {{-- Info kamar --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-800 text-sm truncate">
                                            {{ $room->name }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            No. {{ $room->room_number }} · Lantai {{ $room->floor }}
                                            · {{ $room->capacity }} tamu
                                        </p>
                                        <p class="text-xs font-bold mt-1" style="color:#16a34a;">
                                            Rp {{ number_format($room->price_per_night, 0, ',', '.') }}/malam
                                        </p>
                                    </div>

                                    {{-- Checkmark --}}
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center
                                                                        justify-center flex-shrink-0 transition" :class="selectedRoom && selectedRoom.id === {{ $room->id }}
                                                                     ? 'border-green-500 bg-green-500'
                                                                     : 'border-gray-300'">
                                        <i class="ti ti-check text-white text-xs"
                                            x-show="selectedRoom && selectedRoom.id === {{ $room->id }}">
                                        </i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('room_id')
                            <p class="mt-2 text-xs text-red-500">
                                <i class="ti ti-alert-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <br>

                    {{-- Tanggal Menginap --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ti ti-calendar" style="color:#16a34a;"></i>
                            Tanggal Menginap
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Check-in --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Check-in <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <i class="ti ti-calendar-event absolute left-3 top-1/2
                                                          -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="date" name="check_in" x-model="checkIn" min="{{ now()->format('Y-m-d') }}"
                                        required class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                              @error('check_in') border-red-400 @enderror">
                                </div>
                                @error('check_in')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Check-out --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Check-out <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <i class="ti ti-calendar-event absolute left-3 top-1/2
                                                          -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="date" name="check_out" x-model="checkOut"
                                        :min="checkIn || '{{ now()->addDay()->format('Y-m-d') }}'" required class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                              @error('check_out') border-red-400 @enderror">
                                </div>
                                @error('check_out')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Info jumlah malam --}}
                        <div class="mt-3 px-4 py-3 rounded-xl text-sm flex items-center gap-2" style="background:#f0fdf4;"
                            x-show="nights > 0" x-transition>
                            <i class="ti ti-moon text-green-600"></i>
                            <span class="text-green-700 font-medium">
                                <span class="font-bold" x-text="nights"></span> malam ·
                                <span x-text="rupiah(roomTotal)"></span>
                            </span>
                        </div>
                    </div>
                    <br>
                    {{-- Data Tamu --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ti ti-user" style="color:#16a34a;"></i>
                            Data Tamu
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Nama Lengkap <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                                        placeholder="Nama sesuai KTP" required class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                              @error('guest_name') border-red-400 @enderror">
                                    @error('guest_name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        No. KTP / Passport
                                    </label>
                                    <input type="text" name="guest_id_card" value="{{ old('guest_id_card') }}"
                                        placeholder="Opsional" class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Email <span class="text-red-400">*</span>
                                    </label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                        placeholder="email@contoh.com" required class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                              @error('guest_email') border-red-400 @enderror">
                                    @error('guest_email')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        No. HP <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="guest_phone" value="{{ old('guest_phone') }}"
                                        placeholder="08xxxxxxxxxx" required class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                              rounded-xl bg-gray-50 focus:outline-none
                                                              focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                              @error('guest_phone') border-red-400 @enderror">
                                    @error('guest_phone')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Jumlah Tamu <span class="text-red-400">*</span>
                                    </label>
                                    <select name="num_guests" class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                               rounded-xl bg-gray-50 focus:outline-none
                                                               focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}">{{ $i }} Tamu</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Permintaan Khusus
                                </label>
                                <textarea name="special_requests" rows="2" placeholder="Extra bed, lantai tinggi, dll..."
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                             rounded-xl bg-gray-50 focus:outline-none
                                                             focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                             resize-none">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <br>
                    {{-- Pilih FnB (opsional) --}}
                    @if($fnbItems->count() > 0)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5">
                            <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                                <i class="ti ti-tools-kitchen-2" style="color:#16a34a;"></i>
                                Tambah Pesanan F&B
                            </h3>
                            <p class="text-xs text-gray-400 mb-4">Opsional</p>

                            @foreach($fnbItems as $categoryId => $items)
                                @php $category = $fnbCategories[$categoryId] ?? null @endphp
                                @if($category)
                                    <div class="mb-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-base">{{ $category->icon }}</span>
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                                {{ $category->name }}
                                            </h4>
                                            <div class="flex-1 h-px bg-gray-100"></div>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($items as $fnbItem)
                                                <div class="flex items-center gap-3 p-3 border rounded-xl transition" x-data="{ qty: 0 }"
                                                    :class="qty > 0
                                                                                                                         ? 'border-green-300 bg-green-50'
                                                                                                                         : 'border-gray-100'">

                                                    {{-- Foto --}}
                                                    <div
                                                        class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0
                                                                                                                                bg-gray-50 flex items-center justify-center">
                                                        @if($fnbItem->image)
                                                            <img src="{{ Storage::url($fnbItem->image) }}" class="w-full h-full object-contain"
                                                                alt="{{ $fnbItem->name }}">
                                                        @else
                                                            <i class="ti ti-tools-kitchen-2 text-gray-300"></i>
                                                        @endif
                                                    </div>

                                                    {{-- Info --}}
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-gray-800 text-sm truncate">
                                                            {{ $fnbItem->name }}
                                                        </p>
                                                        <p class="text-xs font-bold mt-0.5" style="color:#16a34a;">
                                                            Rp {{ number_format($fnbItem->price, 0, ',', '.') }}
                                                        </p>
                                                    </div>

                                                    {{-- Qty control --}}
                                                    <div class="flex items-center gap-2 flex-shrink-0">
                                                        <button type="button" @click="
                                                                                                                                    if(qty > 0) {
                                                                                                                                        qty--
                                                                                                                                        $dispatch('fnb-update', {
                                                                                                                                            id: {{ $fnbItem->id }},
                                                                                                                                            price: {{ $fnbItem->price }},
                                                                                                                                            name: '{{ addslashes($fnbItem->name) }}',
                                                                                                                                            qty: qty
                                                                                                                                        })
                                                                                                                                    }
                                                                                                                                "
                                                            class="w-8 h-8 flex items-center justify-center
                                                                                                                                       rounded-full border-2 transition"
                                                            :class="qty > 0
                                                                                                                                    ? 'border-red-300 text-red-500 hover:bg-red-50'
                                                                                                                                    : 'border-gray-200 text-gray-300 cursor-not-allowed'">
                                                            <i class="ti ti-minus text-xs"></i>
                                                        </button>

                                                        <div class="w-8 h-8 flex items-center justify-center
                                                                                                                                    rounded-full"
                                                            :style="qty > 0
                                                                                                                                 ? 'background:#16a34a;'
                                                                                                                                 : 'background:#f3f4f6;'">
                                                            <span class="text-sm font-bold"
                                                                :style="qty > 0 ? 'color:white;' : 'color:#9ca3af;'" x-text="qty"></span>
                                                        </div>

                                                        <input type="hidden" :name="qty > 0 ? 'fnb[{{ $fnbItem->id }}]' : ''" :value="qty">

                                                        <button type="button" @click="
                                                                                                                                    qty++
                                                                                                                                    $dispatch('fnb-update', {
                                                                                                                                        id: {{ $fnbItem->id }},
                                                                                                                                        price: {{ $fnbItem->price }},
                                                                                                                                        name: '{{ addslashes($fnbItem->name) }}',
                                                                                                                                        qty: qty
                                                                                                                                    })
                                                                                                                                "
                                                            class="w-8 h-8 flex items-center justify-center
                                                                                                                                       rounded-full border-2 border-green-300
                                                                                                                                       text-green-600 hover:bg-green-50 transition">
                                                            <i class="ti ti-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </form>
            </div>

            {{-- ==========================================
            KOLOM KANAN — Ringkasan (Sticky)
            ========================================== --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20" @fnb-update.window="setFnb(
                                     $event.detail.id,
                                     $event.detail.price,
                                     $event.detail.name,
                                     $event.detail.qty
                                 )">

                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan</h3>

                    {{-- Kamar dipilih --}}
                    <div class="p-3 rounded-xl mb-3" :style="selectedRoom ? 'background:#f0fdf4;' : 'background:#f9fafb;'">
                        <p class="text-xs text-gray-400 mb-0.5">Kamar</p>
                        <p class="font-bold text-sm" :class="selectedRoom ? 'text-gray-800' : 'text-gray-400 italic'"
                            x-text="selectedRoom ? selectedRoom.name : 'Belum dipilih'">
                        </p>
                    </div>

                    {{-- Rincian harga --}}
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between text-gray-500">
                            <span x-text="'Kamar × ' + nights + ' malam'"></span>
                            <span x-text="rupiah(roomTotal)"></span>
                        </div>

                        <template x-for="(item, id) in fnbItems" :key="id">
                            <div class="flex justify-between text-gray-500">
                                <span class="truncate max-w-28" x-text="item.name + ' ×' + item.qty"></span>
                                <span class="flex-shrink-0 ml-2" x-text="rupiah(item.price * item.qty)"></span>
                            </div>
                        </template>

                        <div class="border-t border-gray-100 pt-2 space-y-1">
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>Subtotal</span>
                                <span x-text="rupiah((roomTotal + fnbTotal))"></span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>Pajak (11%)</span>
                                <span x-text="rupiah((roomTotal + fnbTotal) * 0.11)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Grand total --}}
                    <div class="flex justify-between font-bold text-gray-800 py-3
                                            border-t border-gray-200 mb-4">
                        <span>Total</span>
                        <span style="color:#16a34a;" x-text="rupiah(grandTotal)"></span>
                    </div>

                    {{-- Tombol submit --}}
                    <button type="submit" form="walkin-form"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition" style="background:#16a34a;"
                        :disabled="!selectedRoom || nights < 1" :class="(!selectedRoom || nights < 1)
                                            ? 'opacity-50 cursor-not-allowed'
                                            : 'hover:-translate-y-0.5'">
                        <i class="ti ti-calendar-plus mr-2"></i>
                        Buat Booking Walk-in
                    </button>

                    <a href="{{ route('resepsionis.bookings.index') }}" class="block text-center mt-2 py-2.5 text-sm text-gray-500
                                          hover:text-gray-700 transition">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </div>

@endsection
