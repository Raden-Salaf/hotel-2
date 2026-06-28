<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_id_card',
        'check_in',
        'check_out',
        'num_guests',
        'special_requests',
        'booking_type',
        'status',
        'room_price',
        'fnb_price',
        'total_price',
        'created_by',
    ];

    protected $casts = [
        'check_in'    => 'date',
        'check_out'   => 'date',
        'room_price'  => 'decimal:2',
        'fnb_price'   => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /** Relasi ke user (tamu online) */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi ke kamar yang dipesan */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Relasi ke staff yang input (walk-in) */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Relasi ke item FnB yang dipesan */
    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    /** Relasi ke invoice */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /** Relasi ke pembayaran */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relasi ke pesanan laundry
     */
    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }
}
