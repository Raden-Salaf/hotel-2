<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FnbCategory;
use App\Models\FnbItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FnbItemController extends Controller
{
    public function index()
    {
        // Eager load kategori + filter by kategori jika ada
        $items = FnbItem::with('category')
                        ->when(request('category'), fn($q, $c) => $q->where('fnb_category_id', $c))
                        ->when(request('status') === 'available', fn($q) => $q->where('is_available', true))
                        ->when(request('status') === 'unavailable', fn($q) => $q->where('is_available', false))
                        ->latest()
                        ->paginate(12);

        // Untuk dropdown filter kategori
        $categories = FnbCategory::orderBy('name')->get();

        return view('admin.fnb-items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = FnbCategory::orderBy('name')->get();

        return view('admin.fnb-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fnb_category_id' => 'required|exists:fnb_categories,id',
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'is_available'    => 'boolean',
            'image'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('fnb', 'public');
        }

        // Checkbox is_available — kalau tidak dicentang, nilainya null → kita default ke false
        $validated['is_available'] = $request->boolean('is_available');

        FnbItem::create($validated);

        return redirect()
               ->route('admin.fnb-items.index')
               ->with('success', "Menu '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(FnbItem $fnbItem)
    {
        $categories = FnbCategory::orderBy('name')->get();

        return view('admin.fnb-items.edit', compact('fnbItem', 'categories'));
    }

    public function update(Request $request, FnbItem $fnbItem)
    {
        $validated = $request->validate([
            'fnb_category_id' => 'required|exists:fnb_categories,id',
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'is_available'    => 'boolean',
            'image'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($fnbItem->image) {
                Storage::disk('public')->delete($fnbItem->image);
            }
            $validated['image'] = $request->file('image')->store('fnb', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available');

        $fnbItem->update($validated);

        return redirect()
               ->route('admin.fnb-items.index')
               ->with('success', "Menu '{$validated['name']}' berhasil diperbarui!");
    }

    public function destroy(FnbItem $fnbItem)
    {
        if ($fnbItem->image) {
            Storage::disk('public')->delete($fnbItem->image);
        }

        $name = $fnbItem->name;
        $fnbItem->delete();

        return redirect()
               ->route('admin.fnb-items.index')
               ->with('success', "Menu '{$name}' berhasil dihapus!");
    }
}