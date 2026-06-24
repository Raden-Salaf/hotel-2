<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomCategory extends Model
{
    // Kolom yang boleh diisi massal
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi: Satu kategori punya banyak kamar
     * Digunakan di seeder: $standard->rooms()->create(...)
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}