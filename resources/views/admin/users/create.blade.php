@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('admin.users.index') }}" class="hover:text-green-600 transition">Kelola User</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium">Tambah User</span>
    </div>

    <div class="max-w-lg">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Tambah User Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Buat akun untuk resepsionis atau admin F&B
                </p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nama Lengkap <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap user"
                        autofocus
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

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Email <span class="text-red-400">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@paijohotel.com"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                              bg-gray-50 focus:outline-none focus:border-green-500
                              focus:ring-2 focus:ring-green-100
                              @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Role <span class="text-red-400">*</span>
                    </label>
                    <div class="space-y-2" x-data="{ selected: '{{ old('role') }}' }">
                        @foreach ($roles as $role)
                            @php
                                $roleInfo = [
                                    'super_admin' => [
                                        'label' => 'Super Admin',
                                        'desc' => 'Akses penuh ke semua fitur sistem',
                                        'color' => 'border-purple-500 bg-purple-50 text-purple-700',
                                        'dot' => 'bg-purple-500',
                                    ],
                                    'resepsionis' => [
                                        'label' => 'Resepsionis',
                                        'desc' => 'Kelola booking, check-in & check-out tamu',
                                        'color' => 'border-blue-500 bg-blue-50 text-blue-700',
                                        'dot' => 'bg-blue-500',
                                    ],
                                    'admin_fnb' => [
                                        'label' => 'Admin F&B',
                                        'desc' => 'Kelola menu dan pesanan makanan & minuman',
                                        'color' => 'border-orange-500 bg-orange-50 text-orange-700',
                                        'dot' => 'bg-orange-500',
                                    ],
                                ];
                                $info = $roleInfo[$role->name] ?? [
                                    'label' => $role->name,
                                    'desc' => '',
                                    'color' => 'border-gray-400 bg-gray-50 text-gray-700',
                                    'dot' => 'bg-gray-400',
                                ];
                            @endphp
                            <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition"
                                :class="selected === '{{ $role->name }}'
                                    ?
                                    '{{ $info['color'] }}' :
                                    'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="role" value="{{ $role->name }}" x-model="selected"
                                    class="hidden">
                                {{-- Dot indikator --}}
                                <div class="w-4 h-4 rounded-full border-2 flex items-center
                                    justify-center flex-shrink-0 transition"
                                    :class="selected === '{{ $role->name }}'
                                        ?
                                        'border-current' :
                                        'border-gray-300'">
                                    <div class="w-2 h-2 rounded-full {{ $info['dot'] }} transition"
                                        x-show="selected === '{{ $role->name }}'"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">{{ $info['label'] }}</p>
                                    <p class="text-xs opacity-70">{{ $info['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="mt-1 text-xs text-red-500">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" placeholder="Minimal 8 karakter"
                            class="w-full px-3 py-2.5 pr-10 text-sm border border-gray-200
                                  rounded-xl bg-gray-50 focus:outline-none focus:border-green-500
                                  focus:ring-2 focus:ring-green-100
                                  @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-gray-400 hover:text-gray-600 transition">
                            <i :class="show ? 'ti ti-eye-off' : 'ti ti-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi password --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Konfirmasi Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password"
                            class="w-full px-3 py-2.5 pr-10 text-sm border border-gray-200
                                  rounded-xl bg-gray-50 focus:outline-none focus:border-green-500
                                  focus:ring-2 focus:ring-green-100">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-gray-400 hover:text-gray-600 transition">
                            <i :class="show ? 'ti ti-eye-off' : 'ti ti-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl
                               transition hover:-translate-y-0.5"
                        style="background-color:#16a34a;">
                        <i class="ti ti-user-plus mr-1.5"></i>
                        Tambah User
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-2.5 text-sm font-medium text-gray-500 bg-gray-100
                          rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
