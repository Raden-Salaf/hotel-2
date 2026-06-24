<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbItem extends Model
{
    protected $fillable = [
        'fnb_category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean', // Cast ke boolean supaya bisa pakai true/false
        'price'        => 'decimal:2',
    ];

    /**
     * Relasi: Item ini milik satu kategori FnB
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FnbCategory::class, 'fnb_category_id');
    }

    /**
     * Relasi: Item ini bisa ada di banyak booking_items
     */
    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}