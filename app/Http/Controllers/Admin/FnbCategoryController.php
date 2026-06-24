<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FnbCategory;
use Illuminate\Http\Request;

class FnbCategoryController extends Controller
{
    public function index()
    {
        // Ambil semua kategori beserta jumlah item di dalamnya
        $categories = FnbCategory::withCount('items')->latest()->paginate(10);

        return view('admin.fnb-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.fnb-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:fnb_categories,name',
            'icon' => 'nullable|string|max:10', // emoji icon
        ]);

        FnbCategory::create($validated);

        return redirect()
               ->route('admin.fnb-categories.index')
               ->with('success', "Kategori F&B '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(FnbCategory $fnbCategory)
    {
        return view('admin.fnb-categories.edit', compact('fnbCategory'));
    }

    public function update(Request $request, FnbCategory $fnbCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:fnb_categories,name,' . $fnbCategory->id,
            'icon' => 'nullable|string|max:10',
        ]);

        $fnbCategory->update($validated);

        return redirect()
               ->route('admin.fnb-categories.index')
               ->with('success', "Kategori '{$validated['name']}' berhasil diperbarui!");
    }

    public function destroy(FnbCategory $fnbCategory)
    {
        if ($fnbCategory->items()->exists()) {
            return redirect()
                   ->route('admin.fnb-categories.index')
                   ->with('error', "Kategori '{$fnbCategory->name}' masih memiliki menu. Hapus menunya terlebih dahulu!");
        }

        $name = $fnbCategory->name;
        $fnbCategory->delete();

        return redirect()
               ->route('admin.fnb-categories.index')
               ->with('success', "Kategori '{$name}' berhasil dihapus!");
    }
}