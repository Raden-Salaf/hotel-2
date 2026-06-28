<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaundryItem;
use Illuminate\Http\Request;

class LaundryItemController extends Controller
{
    /**
     * Tampilkan semua item laundry
     */
    public function index()
    {
        $items = LaundryItem::latest()->paginate(15);

        return view('admin.laundry.index', compact('items'));
    }

    /**
     * Form tambah item laundry
     */
    public function create()
    {
        return view('admin.laundry.create');
    }

    /**
     * Simpan item laundry baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'unit'         => 'required|in:pcs,kg',
            'icon'         => 'nullable|string|max:10',
            'is_available' => 'boolean',
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);

        LaundryItem::create($validated);

        return redirect()
            ->route('admin.laundry.index')
            ->with('success', "Item laundry '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Form edit item laundry
     */
    public function edit(LaundryItem $laundry)
    {
        return view('admin.laundry.edit', compact('laundry'));
    }

    /**
     * Update item laundry
     */
    public function update(Request $request, LaundryItem $laundry)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'unit'         => 'required|in:pcs,kg',
            'icon'         => 'nullable|string|max:10',
            'is_available' => 'boolean',
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        $laundry->update($validated);

        return redirect()
            ->route('admin.laundry.index')
            ->with('success', "Item laundry '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus item laundry
     */
    public function destroy(LaundryItem $laundry)
    {
        // Cek apakah masih ada order aktif
        $activeOrders = $laundry->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($activeOrders) {
            return redirect()
                ->route('admin.laundry.index')
                ->with('error', "Item '{$laundry->name}' tidak bisa dihapus karena masih ada pesanan aktif!");
        }

        $name = $laundry->name;
        $laundry->delete();

        return redirect()
            ->route('admin.laundry.index')
            ->with('success', "Item laundry '{$name}' berhasil dihapus!");
    }
}
