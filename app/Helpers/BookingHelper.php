<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Invoice;

class BookingHelper
{
    /**
     * Generate kode booking unik
     * Format: BK-YYYYMMDD-XXXX (contoh: BK-20241201-0001)
     */
    public static function generateBookingCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "BK-{$date}-";

        // Cari booking terakhir hari ini
        $last = Booking::where('booking_code', 'like', $prefix . '%')
            ->orderByDesc('booking_code')
            ->first();

        // Ambil nomor urut terakhir, tambah 1
        $number = $last
            ? (int) substr($last->booking_code, -4) + 1
            : 1;

        // Pad angka jadi 4 digit: 1 → 0001
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate nomor invoice unik
     * Format: INV-YYYYMMDD-XXXX (contoh: INV-20241201-0001)
     */
    public static function generateInvoiceNumber(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "INV-{$date}-";

        $last = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->first();

        $number = $last
            ? (int) substr($last->invoice_number, -4) + 1
            : 1;

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung jumlah malam dari check-in ke check-out
     */
    public static function calculateNights(string $checkIn, string $checkOut): int
    {
        return (int) now()->parse($checkIn)->diffInDays($checkOut);
    }

    /**
     * Hitung total harga kamar
     */
    public static function calculateRoomPrice(float $pricePerNight, int $nights): float
    {
        return $pricePerNight * $nights;
    }
}
