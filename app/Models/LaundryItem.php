<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'unit',
        'is_available',
        'icon',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Relasi ke pesanan laundry
     */
    public function orders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }
}
