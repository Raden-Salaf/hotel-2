<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * INDEX — Tampilkan semua kamar
     * Dipanggil saat: GET /admin/rooms
     */
    public function index()
    {
        // with('category') = eager loading — ambil data kategori sekalian
        // tanpa ini, nanti tiap baris kamar akan query ke DB lagi (N+1 problem)
        // paginate(10) = tampilkan 10 kamar per halaman
        $rooms = Room::with('category')
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * CREATE — Tampilkan form tambah kamar baru
     * Dipanggil saat: GET /admin/rooms/create
     */
    public function create()
    {
        // Ambil semua kategori untuk dropdown pilihan
        $categories = RoomCategory::orderBy('name')->get();

        return view('admin.rooms.create', compact('categories'));
    }

    /**
     * STORE — Simpan kamar baru ke database
     * Dipanggil saat: POST /admin/rooms
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        // Setiap rule dipisah dengan | atau bisa pakai array
        $validated = $request->validate([
            'room_category_id' => 'required|exists:room_categories,id',
            'room_number'      => 'required|string|unique:rooms,room_number',
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'price_per_night'  => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1',
            'floor'            => 'nullable|string|max:10',
            'status'           => 'required|in:available,occupied,maintenance',
            'facilities'       => 'nullable|array',   // dari checkbox, datanya array
            'image'            => 'nullable|image|max:2048', // max 2MB
        ]);

        // Handle upload foto kamar
        if ($request->hasFile('image')) {
            // store() otomatis generate nama file unik dan simpan di storage/app/public/rooms
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        // facilities dari checkbox form → encode ke JSON untuk disimpan di DB
        $validated['facilities'] = json_encode($request->input('facilities', []));

        // Buat record baru di database
        Room::create($validated);

        // redirect() dengan flash message
        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan!');
    }

    /**
     * SHOW — Tampilkan detail satu kamar
     * Dipanggil saat: GET /admin/rooms/{room}
     * Laravel otomatis inject model Room lewat Route Model Binding
     */
    public function show(Room $room)
    {
        // load relasi yang belum di-load
        $room->load('category', 'bookings');

        return view('admin.rooms.show', compact('room'));
    }

    /**
     * EDIT — Tampilkan form edit kamar
     * Dipanggil saat: GET /admin/rooms/{room}/edit
     */
    public function edit(Room $room)
    {
        $categories = RoomCategory::orderBy('name')->get();

        return view('admin.rooms.edit', compact('room', 'categories'));
    }

    /**
     * UPDATE — Simpan perubahan kamar ke database
     * Dipanggil saat: PUT /admin/rooms/{room}
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_category_id' => 'required|exists:room_categories,id',
            // unique tapi abaikan ID kamar ini sendiri (supaya tidak bentrok dengan data sendiri)
            'room_number'      => 'required|string|unique:rooms,room_number,' . $room->id,
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'price_per_night'  => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1',
            'floor'            => 'nullable|string|max:10',
            'status'           => 'required|in:available,occupied,maintenance',
            'facilities'       => 'nullable|array',
            'image'            => 'nullable|image|max:2048',
        ]);

        // Jika ada foto baru di-upload
        if ($request->hasFile('image')) {
            // Hapus foto lama dari storage supaya tidak menumpuk
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $validated['facilities'] = json_encode($request->input('facilities', []));

        // update() langsung mengubah data di DB
        $room->update($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil diperbarui!');
    }

    /**
     * DESTROY — Hapus kamar dari database
     * Dipanggil saat: DELETE /admin/rooms/{room}
     */
    public function destroy(Room $room)
    {
        // Cek dulu apakah kamar sedang dipakai di booking aktif
        $activeBooking = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();

        if ($activeBooking) {
            return redirect()
                ->route('admin.rooms.index')
                ->with('error', 'Kamar tidak bisa dihapus karena masih ada booking aktif!');
        }

        // Hapus foto dari storage jika ada
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil dihapus!');
    }
}
