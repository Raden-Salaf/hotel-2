<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryOrder extends Model
{
    protected $fillable = [
        'booking_id',
        'laundry_item_id',
        'quantity',
        'price',
        'subtotal',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi ke item laundry
     */
    public function laundryItem(): BelongsTo
    {
        return $this->belongsTo(LaundryItem::class);
    }

    /**
     * Relasi ke user yang input
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}