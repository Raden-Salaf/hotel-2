@extends('layouts.app')

@section('title', 'Edit Booking — ' . $booking->booking_code)

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('resepsionis.bookings.index') }}"
       class="hover:text-green-600 transition">Booking</a>
    <i class="ti ti-chevron-right text-xs"></i>
    <a href="{{ route('resepsionis.bookings.show', $booking) }}"
       class="hover:text-green-600 transition">{{ $booking->booking_code }}</a>
    <i class="ti ti-chevron-right text-xs"></i>
    <span class="text-gray-600 font-medium">Edit</span>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Edit Data Booking</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                Hanya data tamu yang bisa diubah.
                Kamar & tanggal tidak bisa diubah setelah booking dibuat.
            </p>
        </div>

        <form action="{{ route('resepsionis.bookings.update', $booking) }}"
              method="POST"
              class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Info booking — read only --}}
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">
                    Info Booking (Tidak Bisa Diubah)
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Kode Booking</p>
                        <p class="font-mono font-bold" style="color:#16a34a;">
                            {{ $booking->booking_code }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Kamar</p>
                        <p class="font-semibold text-gray-800">
                            {{ $booking->room->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Check-in</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Check-out</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Data tamu yang bisa diedit --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">
                    Data Tamu
                </p>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Nama Lengkap <span class="text-red-400">*</span>
                            </label>
                            <input type="text"
                                   name="guest_name"
                                   value="{{ old('guest_name', $booking->guest_name) }}"
                                   required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200
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
                            <input type="text"
                                   name="guest_id_card"
                                   value="{{ old('guest_id_card', $booking->guest_id_card) }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200
                                          rounded-xl bg-gray-50 focus:outline-none
                                          focus:border-green-500 focus:ring-2 focus:ring-green-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Email <span class="text-red-400">*</span>
                            </label>
                            <input type="email"
                                   name="guest_email"
                                   value="{{ old('guest_email', $booking->guest_email) }}"
                                   required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200
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
                            <input type="text"
                                   name="guest_phone"
                                   value="{{ old('guest_phone', $booking->guest_phone) }}"
                                   required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200
                                          rounded-xl bg-gray-50 focus:outline-none
                                          focus:border-green-500 focus:ring-2 focus:ring-green-100
                                          @error('guest_phone') border-red-400 @enderror">
                            @error('guest_phone')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Permintaan Khusus
                        </label>
                        <textarea name="special_requests"
                                  rows="3"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-200
                                         rounded-xl bg-gray-50 focus:outline-none
                                         focus:border-green-500 focus:ring-2 focus:ring-green-100
                                         resize-none">{{ old('special_requests', $booking->special_requests) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl
                               transition hover:-translate-y-0.5"
                        style="background-color:#16a34a;">
                    <i class="ti ti-device-floppy mr-1.5"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('resepsionis.bookings.show', $booking) }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100
                          rounded-xl hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection