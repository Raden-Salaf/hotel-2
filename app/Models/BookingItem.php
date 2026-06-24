<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    protected $fillable = [
        'booking_id',
        'fnb_item_id',
        'quantity',
        'price',
        'subtotal',
        'status',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /** Relasi ke booking induknya */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** Relasi ke menu FnB yang dipesan */
    public function fnbItem(): BelongsTo
    {
        return $this->belongsTo(FnbItem::class);
    }
}