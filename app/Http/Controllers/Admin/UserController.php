<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Tampilkan semua user
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($q, $s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            })
            ->when($request->role, function ($q, $r) {
                $q->whereHas('roles', fn($q) => $q->where('name', $r));
            })
            ->latest()
            ->paginate(10);

        // Ambil semua role untuk filter
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Form tambah user baru
     */
    public function create()
    {
        // Ambil semua role yang tersedia
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        // Buat user baru
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role ke user
        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$validated['name']} berhasil ditambahkan!");
    }

    /**
     * Detail user
     */
    public function show(User $user)
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            // Password opsional saat edit — hanya diupdate jika diisi
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        // Update data user
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            // Hanya update password jika diisi
            ...($validated['password']
                ? ['password' => Hash::make($validated['password'])]
                : []),
        ]);

        // Sync role — hapus role lama, assign role baru
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$validated['name']} berhasil diperbarui!");
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Cegah super admin hapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $name = $user->name;
        $user->delete();

        // Alert::success('berhasil', 'berhasil hapus');
        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$name} berhasil dihapus!");
    }
}
