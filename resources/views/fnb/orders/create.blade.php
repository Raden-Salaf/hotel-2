@extends('layouts.app')

@section('title', 'Buat Pesanan F&B')

@section('content')

<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('fnb.orders.index') }}"
       class="hover:text-green-600 transition">Pesanan F&B</a>
    <i class="ti ti-chevron-right text-xs"></i>
    <span class="text-gray-600 font-medium">Buat Pesanan Baru</span>
</div>

<div class="max-w-4xl"
     x-data="{
         selectedBooking: null,
         fnbItems: {},

         get fnbTotal() {
             let total = 0
             Object.values(this.fnbItems).forEach(i => total += i.price * i.qty)
             return total
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
     }"
     @fnb-update.window="setFnb(
         $event.detail.id,
         $event.detail.price,
         $event.detail.name,
         $event.detail.qty
     )">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom kiri: Form --}}
        <div class="lg:col-span-2 space-y-5">

            <form action="{{ route('fnb.orders.store-order') }}"
                  method="POST"
                  id="order-form">
                @csrf

                {{-- Pilih Booking Tamu --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ti ti-user-check" style="color:#16a34a;"></i>
                        Pilih Tamu
                    </h3>

                    @if($bookings->isEmpty())
                        {{-- Tidak ada booking aktif --}}
                        <div class="py-8 text-center">
                            <i class="ti ti-calendar-off text-4xl text-gray-200 block mb-3"></i>
                            <p class="text-gray-400 text-sm">
                                Tidak ada tamu yang sedang confirmed atau check-in
                            </p>
                        </div>
                    @else
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                            @foreach($bookings as $booking)
                            <label class="flex items-center gap-4 p-4 rounded-xl border-2
                                          cursor-pointer transition"
                                   :class="selectedBooking === {{ $booking->id }}
                                       ? 'border-green-500 bg-green-50'
                                       : 'border-gray-100 hover:border-gray-200'"
                                   @click="selectedBooking = {{ $booking->id }}">

                                <input type="radio"
                                       name="booking_id"
                                       value="{{ $booking->id }}"
                                       class="hidden">

                                {{-- Status badge --}}
                                <div class="flex-shrink-0">
                                    @if($booking->status === 'checked_in')
                                        <span class="px-2 py-1 rounded-lg text-xs font-bold
                                                     bg-green-100 text-green-700">
                                            Check-in
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-lg text-xs font-bold
                                                     bg-blue-100 text-blue-700">
                                            Confirmed
                                        </span>
                                    @endif
                                </div>

                                {{-- Info tamu --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm">
                                        {{ $booking->guest_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Kamar {{ $booking->room->room_number }} —
                                        {{ $booking->room->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }}
                                        →
                                        {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                    </p>
                                </div>

                                {{-- Kode booking --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="font-mono text-xs font-bold"
                                       style="color:#16a34a;">
                                        {{ $booking->booking_code }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $booking->bookingItems->count() }} item sebelumnya
                                    </p>
                                </div>

                                {{-- Checkmark --}}
                                <div class="w-6 h-6 rounded-full border-2 flex items-center
                                            justify-center flex-shrink-0 transition"
                                     :class="selectedBooking === {{ $booking->id }}
                                         ? 'border-green-500 bg-green-500'
                                         : 'border-gray-300'">
                                    <i class="ti ti-check text-white text-xs"
                                       x-show="selectedBooking === {{ $booking->id }}"></i>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif

                    @error('booking_id')
                        <p class="mt-2 text-xs text-red-500">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Pilih Menu FnB --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ti ti-tools-kitchen-2" style="color:#16a34a;"></i>
                        Pilih Menu
                    </h3>

                    @if($fnbItems->isEmpty())
                        <div class="py-8 text-center">
                            <i class="ti ti-tools-kitchen-2 text-4xl text-gray-200 block mb-3"></i>
                            <p class="text-gray-400 text-sm">Tidak ada menu tersedia</p>
                        </div>
                    @else
                        @foreach($fnbItems as $categoryId => $items)
                            @php $category = $fnbCategories[$categoryId] ?? null @endphp
                            @if($category)
                            <div class="mb-5">
                                {{-- Header kategori --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-lg">{{ $category->icon }}</span>
                                    <h4 class="text-sm font-bold text-gray-700">
                                        {{ $category->name }}
                                    </h4>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>

                                <div class="space-y-2">
                                    @foreach($items as $fnbItem)
                                    <div class="flex items-center gap-3 p-3 rounded-xl border transition"
                                         x-data="{ qty: 0 }"
                                         :class="qty > 0
                                             ? 'border-green-300 bg-green-50'
                                             : 'border-gray-100 hover:border-gray-200'">

                                        {{-- Foto --}}
                                        <div class="w-12 h-12 rounded-xl overflow-hidden
                                                    flex-shrink-0 bg-gray-100
                                                    flex items-center justify-center">
                                            @if($fnbItem->image)
                                                <img src="{{ Storage::url($fnbItem->image) }}"
                                                     class="w-full h-full object-contain"
                                                     alt="{{ $fnbItem->name }}">
                                            @else
                                                <i class="ti ti-tools-kitchen-2 text-gray-300"></i>
                                            @endif
                                        </div>

                                        {{-- Info menu --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-800 text-sm">
                                                {{ $fnbItem->name }}
                                            </p>
                                            <p class="text-xs font-bold mt-0.5"
                                               style="color:#16a34a;">
                                                Rp {{ number_format($fnbItem->price, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        {{-- Qty control --}}
                                        <div class="flex items-center gap-3 flex-shrink-0">

                                            {{-- Tombol kurang --}}
                                            <button type="button"
                                                    @click="
                                                        if (qty > 0) {
                                                            qty--
                                                            $dispatch('fnb-update', {
                                                                id:    {{ $fnbItem->id }},
                                                                price: {{ $fnbItem->price }},
                                                                name:  '{{ addslashes($fnbItem->name) }}',
                                                                qty:   qty
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

                                            {{-- Display qty --}}
                                            <div class="w-8 h-8 flex items-center justify-center
                                                        rounded-full"
                                                 :style="qty > 0
                                                     ? 'background:#16a34a;'
                                                     : 'background:#f3f4f6;'">
                                                <span class="text-sm font-bold"
                                                      :style="qty > 0
                                                          ? 'color:white;'
                                                          : 'color:#9ca3af;'"
                                                      x-text="qty"></span>
                                            </div>

                                            {{-- Hidden input --}}
                                            <input type="hidden"
                                                   :name="qty > 0 ? 'fnb[{{ $fnbItem->id }}]' : ''"
                                                   :value="qty">

                                            {{-- Tombol tambah --}}
                                            <button type="button"
                                                    @click="
                                                        qty++
                                                        $dispatch('fnb-update', {
                                                            id:    {{ $fnbItem->id }},
                                                            price: {{ $fnbItem->price }},
                                                            name:  '{{ addslashes($fnbItem->name) }}',
                                                            qty:   qty
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
                    @endif

                    @error('fnb')
                        <p class="mt-2 text-xs text-red-500">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

            </form>
        </div>

        {{-- Kolom kanan: Ringkasan --}}
        <div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">

                <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h3>

                {{-- Tamu dipilih --}}
                <div class="p-3 rounded-xl bg-gray-50 mb-4">
                    <p class="text-xs text-gray-400 mb-0.5">Tamu</p>
                    <template x-if="selectedBooking">
                        {{-- Tampil nama tamu dari data PHP --}}
                        @foreach($bookings as $booking)
                        <div x-show="selectedBooking === {{ $booking->id }}">
                            <p class="font-bold text-gray-800 text-sm">
                                {{ $booking->guest_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Kamar {{ $booking->room->room_number }}
                            </p>
                        </div>
                        @endforeach
                    </template>
                    <template x-if="!selectedBooking">
                        <p class="text-xs text-gray-400 italic">Belum dipilih</p>
                    </template>
                </div>

                {{-- Item yang dipilih --}}
                <div class="space-y-2 mb-4 min-h-16">
                    <template x-for="(item, id) in fnbItems" :key="id">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 truncate max-w-32"
                                  x-text="item.name + ' ×' + item.qty"></span>
                            <span class="font-medium text-gray-800 flex-shrink-0 ml-2"
                                  x-text="rupiah(item.price * item.qty)"></span>
                        </div>
                    </template>

                    {{-- Placeholder jika belum ada item --}}
                    <template x-if="Object.keys(fnbItems).length === 0">
                        <p class="text-xs text-gray-400 italic text-center py-3">
                            Belum ada menu dipilih
                        </p>
                    </template>
                </div>

                {{-- Total --}}
                <div class="flex justify-between font-bold text-gray-800 py-3
                            border-t border-gray-200 mb-4">
                    <span>Total Pesanan</span>
                    <span style="color:#16a34a;" x-text="rupiah(fnbTotal)"></span>
                </div>

                {{-- Catatan --}}
                <div class="p-3 rounded-xl mb-4 text-xs text-blue-700 bg-blue-50
                            border border-blue-100">
                    <i class="ti ti-info-circle mr-1"></i>
                    Total ini akan ditambahkan ke invoice tamu yang dipilih
                </div>

                {{-- Tombol submit --}}
                <button type="submit"
                        form="order-form"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition"
                        style="background:#16a34a;"
                        :disabled="!selectedBooking || Object.keys(fnbItems).length === 0"
                        :class="(!selectedBooking || Object.keys(fnbItems).length === 0)
                            ? 'opacity-50 cursor-not-allowed'
                            : 'hover:-translate-y-0.5'">
                    <i class="ti ti-send mr-2"></i>
                    Buat Pesanan
                </button>

                <a href="{{ route('fnb.orders.index') }}"
                   class="block text-center mt-2 py-2.5 text-sm text-gray-500
                          hover:text-gray-700 transition">
                    Batal
                </a>
            </div>
        </div>

    </div>
</div>

@endsection