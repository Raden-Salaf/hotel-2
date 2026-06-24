<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'room_category_id',
        'room_number',
        'name',
        'description',
        'price_per_night',
        'capacity',
        'floor',
        'status',
        'image',
        'facilities',
    ];

    // Cast kolom facilities dari JSON string jadi array otomatis
    protected $casts = [
        'facilities' => 'array',
    ];

    /**
     * Relasi: Kamar ini milik satu kategori
     */

     public function getFacilitiesAttribute($value): array
    {
        // Jika value null → return array kosong
        if (is_null($value)) return [];

        // Jika value sudah array (karena cast) → langsung return
        if (is_array($value)) return $value;

        // Jika value masih string JSON → decode dulu
        $decoded = json_decode($value, true);

        // json_decode gagal atau hasilnya bukan array → return array kosong
        return is_array($decoded) ? $decoded : [];
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'room_category_id');
    }

    /**
     * Relasi: Satu kamar bisa punya banyak booking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}