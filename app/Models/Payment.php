<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'booking_id',
        'transaction_id',
        'order_id',
        'payment_type',
        'amount',
        'status',
        'midtrans_response',
        'paid_at',
    ];

    protected $casts = [
        'amount'             => 'decimal:2',
        'midtrans_response'  => 'array', // JSON otomatis jadi array
        'paid_at'            => 'datetime',
    ];

    /** Relasi ke invoice */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** Relasi ke booking */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}