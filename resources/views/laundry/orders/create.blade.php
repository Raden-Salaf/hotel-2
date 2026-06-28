@extends('layouts.app')

@section('title', 'Buat Pesanan Laundry')

@section('content')

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('laundry.orders.index') }}" class="hover:text-green-600 transition">Pesanan Laundry</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Buat Pesanan Baru</span>
    </div>

    <div class="max-w-4xl" x-data="{
        selectedBooking: null,
        items: [{ laundry_item_id: '', quantity: 1, notes: '' }],
    
        addItem() {
            this.items.push({ laundry_item_id: '', quantity: 1, notes: '' })
        },
    
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1)
            }
        },
    
        get total() {
            // Hitung total dari semua item yang dipilih
            return 0
        }
    }">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom kiri: Form --}}
            <div class="lg:col-span-2 space-y-5">

                <form action="{{ route('laundry.orders.store') }}" method="POST" id="laundry-form">
                    @csrf

                    {{-- Pilih Tamu --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ti ti-user-check" style="color:#16a34a;"></i>
                            Pilih Tamu
                        </h3>

                        @if ($bookings->isEmpty())
                            <div class="py-8 text-center">
                                <i class="ti ti-calendar-off text-4xl text-gray-200 block mb-3"></i>
                                <p class="text-gray-400 text-sm">
                                    Tidak ada tamu yang sedang confirmed atau check-in
                                </p>
                            </div>
                        @else
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                                @foreach ($bookings as $booking)
                                    <label
                                        class="flex items-center gap-4 p-4 rounded-xl border-2
                                          cursor-pointer transition"
                                        :class="selectedBooking === {{ $booking->id }} ?
                                            'border-green-500 bg-green-50' :
                                            'border-gray-100 hover:border-gray-200'"
                                        @click="selectedBooking = {{ $booking->id }}">

                                        <input type="radio" name="booking_id" value="{{ $booking->id }}" class="hidden">

                                        {{-- Status badge --}}
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs font-bold flex-shrink-0
                                             {{ $booking->status === 'checked_in' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $booking->status === 'checked_in' ? 'Check-in' : 'Confirmed' }}
                                        </span>

                                        {{-- Info tamu --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-gray-800 text-sm">
                                                {{ $booking->guest_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Kamar {{ $booking->room->room_number }} —
                                                {{ $booking->room->name }}
                                            </p>
                                        </div>

                                        {{-- Kode booking --}}
                                        <p class="font-mono text-xs font-bold flex-shrink-0" style="color:#16a34a;">
                                            {{ $booking->booking_code }}
                                        </p>

                                        {{-- Checkmark --}}
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center
                                            justify-center flex-shrink-0"
                                            :class="selectedBooking === {{ $booking->id }} ?
                                                'border-green-500 bg-green-500' :
                                                'border-gray-300'">
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

                    {{-- Pilih Item Laundry --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-xl">👕</span>
                                Item Laundry
                            </h3>
                            {{-- Tombol tambah baris --}}
                            <button type="button" @click="addItem()"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold
                                       text-white rounded-lg transition"
                                style="background:#16a34a;">
                                <i class="ti ti-plus text-sm"></i>
                                Tambah Item
                            </button>
                        </div>

                        {{-- Header kolom --}}
                        <div class="grid grid-cols-12 gap-3 mb-2 px-1">
                            <div class="col-span-5 text-xs font-semibold text-gray-500">Item</div>
                            <div class="col-span-2 text-xs font-semibold text-gray-500">Qty</div>
                            <div class="col-span-4 text-xs font-semibold text-gray-500">Catatan</div>
                            <div class="col-span-1"></div>
                        </div>

                        {{-- Baris item --}}
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="grid grid-cols-12 gap-3 items-start">

                                    {{-- Pilih item laundry --}}
                                    <div class="col-span-5">
                                        <select :name="`items[${index}][laundry_item_id]`" x-model="item.laundry_item_id"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                   rounded-xl bg-gray-50 focus:outline-none
                                                   focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                            <option value="">-- Pilih Item --</option>
                                            @foreach ($laundryItems as $laundryItem)
                                                <option value="{{ $laundryItem->id }}"
                                                    data-price="{{ $laundryItem->price }}"
                                                    data-unit="{{ $laundryItem->unit }}">
                                                    {{ $laundryItem->icon }} {{ $laundryItem->name }}
                                                    (Rp
                                                    {{ number_format($laundryItem->price, 0, ',', '.') }}/{{ $laundryItem->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Qty --}}
                                    <div class="col-span-2">
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                            min="1" placeholder="1"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                  rounded-xl bg-gray-50 focus:outline-none
                                                  focus:border-green-500 focus:ring-2 focus:ring-green-100
                                                  text-center">
                                    </div>

                                    {{-- Catatan --}}
                                    <div class="col-span-4">
                                        <input type="text" :name="`items[${index}][notes]`" x-model="item.notes"
                                            placeholder="Catatan (opsional)"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-200
                                                  rounded-xl bg-gray-50 focus:outline-none
                                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    </div>

                                    {{-- Tombol hapus baris --}}
                                    <div class="col-span-1 flex items-center justify-center pt-1">
                                        <button type="button" @click="removeItem(index)"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg bg-red-50 text-red-400
                                                   hover:bg-red-100 transition"
                                            :class="items.length === 1 ?
                                                'opacity-30 cursor-not-allowed' :
                                                ''">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @error('items')
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

                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan</h3>

                    {{-- Tamu dipilih --}}
                    <div class="p-3 rounded-xl bg-gray-50 mb-4">
                        <p class="text-xs text-gray-400 mb-1">Tamu</p>
                        @foreach ($bookings as $booking)
                            <div x-show="selectedBooking === {{ $booking->id }}">
                                <p class="font-bold text-gray-800 text-sm">{{ $booking->guest_name }}</p>
                                <p class="text-xs text-gray-500">
                                    Kamar {{ $booking->room->room_number }}
                                </p>
                            </div>
                        @endforeach
                        <p x-show="!selectedBooking" class="text-xs text-gray-400 italic">
                            Belum dipilih
                        </p>
                    </div>

                    {{-- Info --}}
                    <div class="p-3 rounded-xl mb-4 text-xs text-blue-700 bg-blue-50 border border-blue-100">
                        <i class="ti ti-info-circle mr-1"></i>
                        Total laundry akan ditambahkan ke invoice tamu
                    </div>

                    {{-- Tombol submit --}}
                    <button type="submit" form="laundry-form"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition" style="background:#16a34a;"
                        :disabled="!selectedBooking"
                        :class="!selectedBooking
                            ?
                            'opacity-50 cursor-not-allowed' :
                            'hover:-translate-y-0.5'">
                        <i class="ti ti-send mr-2"></i>
                        Buat Pesanan Laundry
                    </button>

                    <a href="{{ route('laundry.orders.index') }}"
                        class="block text-center mt-2 py-2.5 text-sm text-gray-500
                          hover:text-gray-700 transition">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
