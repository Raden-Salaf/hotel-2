@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kelola User</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                Total {{ $users->total() }} user terdaftar
            </p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white
              rounded-xl transition hover:-translate-y-0.5"
            style="background-color:#16a34a;">
            <i class="ti ti-user-plus"></i>
            Tambah User
        </a>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-3 flex-wrap">

            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <i
                    class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2
                      text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl
                          bg-gray-50 focus:outline-none focus:border-green-500">
            </div>

            {{-- Filter role --}}
            <select name="role"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50
                       focus:outline-none focus:border-green-500">
                <option value="">Semua Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-xl"
                style="background:#16a34a;">
                <i class="ti ti-filter mr-1"></i> Filter
            </button>

            @if (request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-xl
                      hover:bg-gray-200 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel user --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wide">
                        <th class="text-left px-6 py-3">User</th>
                        <th class="text-left px-6 py-3">Email</th>
                        <th class="text-left px-6 py-3">Role</th>
                        <th class="text-left px-6 py-3">Bergabung</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Avatar + nama --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar inisial --}}
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center
                                        flex-shrink-0 font-bold text-sm"
                                        style="background:#166534; color:#4ade80;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                        {{-- Tandai akun sendiri --}}
                                        @if ($user->id === auth()->id())
                                            <span class="text-xs text-green-600 font-medium">
                                                (Anda)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>

                            {{-- Badge role --}}
                            <td class="px-6 py-4">
                                @php
                                    $roleConfig = [
                                        'super_admin' => 'bg-purple-100 text-purple-700',
                                        'resepsionis' => 'bg-blue-100 text-blue-700',
                                        'admin_fnb' => 'bg-orange-100 text-orange-700',
                                    ];
                                    $roleName = $user->roles->first()?->name ?? '-';
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                     {{ $roleConfig[$roleName] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            {{-- Tombol aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg
                                      bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                        title="Edit">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    {{-- Hapus — tidak bisa hapus diri sendiri --}}
                                    @if ($user->id !== auth()->id())
                                        <button type="button"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                               bg-red-50 text-red-500 hover:bg-red-100 transition"
                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                            onclick="confirmDeleteUser(this.dataset.id, this.dataset.name)" title="Hapus">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>

                                        {{-- Form hapus tersembunyi --}}
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            id="del-user-{{ $user->id }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        {{-- Placeholder agar layout tidak geser --}}
                                        <div class="w-8 h-8"></div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <i class="ti ti-users-off text-5xl text-gray-200 block mb-3"></i>
                                <p class="text-gray-400">Tidak ada user ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function confirmDeleteUser(id, name) {
            Swal.fire({
                title: 'Hapus User?',
                html: `User <strong>"${name}"</strong> akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`del-user-${id}`).submit()
                }
            })
        }
    </script>
@endpush
