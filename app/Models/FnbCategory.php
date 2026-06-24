<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
    ];

    /**
     * Relasi: Satu kategori FnB punya banyak item menu
     * Digunakan di seeder: $makanan->items()->create(...)
     */
    public function items(): HasMany
    {
        return $this->hasMany(FnbItem::class);
    }
}