<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'booking_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'subtotal'  => 'decimal:2',
        'tax'       => 'decimal:2',
        'discount'  => 'decimal:2',
        'total'     => 'decimal:2',
        'due_date'  => 'date',
        'paid_at'   => 'date',
    ];

    /** Relasi ke booking */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** Relasi ke pembayaran */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}