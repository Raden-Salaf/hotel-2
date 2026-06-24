<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomCategoryController extends Controller
{
    /**
     * Tampilkan semua kategori kamar
     */
    public function index()
    {
        // withCount('rooms') = tambahkan kolom rooms_count otomatis
        // berguna untuk tampilkan "berapa kamar di kategori ini"
        $categories = RoomCategory::withCount('rooms')
            ->latest()
            ->paginate(10);

        return view('admin.room-categories.index', compact('categories'));
    }

    /**
     * Tampilkan form tambah kategori
     */
    public function create()
    {
        return view('admin.room-categories.create');
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:room_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        RoomCategory::create($validated);

        return redirect()
            ->route('admin.room-categories.index')
            ->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Tampilkan form edit kategori
     */
    public function edit(RoomCategory $roomCategory)
    {
        return view('admin.room-categories.edit', compact('roomCategory'));
    }

    /**
     * Simpan perubahan kategori
     */
    public function update(Request $request, RoomCategory $roomCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:room_categories,name,' . $roomCategory->id,
            'description' => 'nullable|string|max:500',
        ]);

        $roomCategory->update($validated);

        return redirect()
            ->route('admin.room-categories.index')
            ->with('success', "Kategori '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus kategori
     */
    public function destroy(RoomCategory $roomCategory)
    {
        // Cek apakah masih ada kamar di kategori ini
        if ($roomCategory->rooms()->exists()) {
            return redirect()
                ->route('admin.room-categories.index')
                ->with('error', "Kategori '{$roomCategory->name}' tidak bisa dihapus karena masih memiliki kamar!");
        }

        $name = $roomCategory->name;
        $roomCategory->delete();

        return redirect()
            ->route('admin.room-categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus!");
    }
}
