<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        // Setup konfigurasi Midtrans dari config file
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // Aktifkan sanitasi & validasi input
        Config::$isSanitized = true;
        Config::$is3ds       = true; // 3D Secure untuk keamanan kartu kredit
    }

    /**
     * Buat Snap Token untuk halaman pembayaran Midtrans
     * Token ini yang akan dipakai Snap.js untuk tampilkan popup payment
     *
     * @param Booking $booking — data booking yang akan dibayar
     * @return string — snap token
     */
    public function createSnapToken(Booking $booking): string
    {
        // Load invoice untuk dapat total harga
        $booking->load(['invoice', 'room', 'bookingItems.fnbItem']);

        $invoice = $booking->invoice;

        // Buat order ID unik — gabungkan booking_code + timestamp
        // agar tidak bentrok jika tamu coba bayar ulang
        $orderId = $booking->booking_code . '-' . time();

        // Simpan order_id ke payment record dulu
        // agar bisa ditrack saat callback dari Midtrans
        $payment = Payment::create([
            'invoice_id'   => $invoice->id,
            'booking_id'   => $booking->id,
            'order_id'     => $orderId,
            'amount'       => $invoice->total,
            'status'       => 'pending',
        ]);

        // === Buat parameter untuk dikirim ke Midtrans ===

        // Detail transaksi
        $transactionDetails = [
            'order_id'     => $orderId,
            'gross_amount' => (int) $invoice->total, // harus integer, bukan decimal
        ];

        // Info pelanggan
        $customerDetails = [
            'first_name' => $booking->guest_name,
            'email'      => $booking->guest_email,
            'phone'      => $booking->guest_phone,
        ];

        // Rincian item yang dibeli
        // Midtrans butuh ini untuk validasi gross_amount
        $itemDetails = [];

        // Tambah item kamar
        $itemDetails[] = [
            'id'       => 'ROOM-' . $booking->room->id,
            'price'    => (int) $booking->room_price,
            'quantity' => 1,
            'name'     => substr($booking->room->name, 0, 50), // max 50 karakter
        ];

        // Tambah item FnB jika ada
        foreach ($booking->bookingItems as $item) {
            $itemDetails[] = [
                'id'       => 'FNB-' . $item->fnb_item_id,
                'price'    => (int) $item->price,
                'quantity' => $item->quantity,
                'name'     => substr($item->fnbItem->name, 0, 50),
            ];
        }

        // Tambah item pajak
        $itemDetails[] = [
            'id'       => 'TAX',
            'price'    => (int) $invoice->tax,
            'quantity' => 1,
            'name'     => 'Pajak (11%)',
        ];

        // Gabungkan semua parameter
        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
            // URL callback setelah pembayaran selesai
            'callbacks'           => [
                'finish' => route('public.payment.finish', $booking->id),
            ],
        ];

        // Minta snap token ke Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Simpan snap token ke payment record
        $payment->update(['transaction_id' => $snapToken]);

        return $snapToken;
    }

    /**
     * Handle notifikasi dari Midtrans (webhook)
     * Midtrans akan POST ke URL kita setiap ada update status pembayaran
     *
     * @return array — data notifikasi dari Midtrans
     */
    public function handleNotification(): array
    {
        // Buat instance Notification — otomatis ambil data dari request
        $notification = new Notification();

        // Ambil data penting dari notifikasi
        $orderId           = $notification->order_id;
        $statusCode        = $notification->status_code;
        $grossAmount       = $notification->gross_amount;
        $signatureKey      = $notification->signature_key;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus       = $notification->fraud_status;

        // === Verifikasi signature key ===
        // Ini penting untuk pastikan request benar-benar dari Midtrans
        // bukan dari pihak lain yang coba manipulasi
        $serverKey         = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            throw new \Exception('Invalid signature key');
        }

        // Tentukan status payment kita berdasarkan status dari Midtrans
        $paymentStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

        return [
            'order_id'          => $orderId,
            'transaction_status' => $transactionStatus,
            'payment_status'    => $paymentStatus,
            'payment_type'      => $notification->payment_type,
            'transaction_id'    => $notification->transaction_id,
        ];
    }

    /**
     * Mapping status transaksi Midtrans ke status payment kita
     *
     * Status Midtrans:
     * - capture    = kartu kredit berhasil
     * - settlement = transfer/ewallet berhasil
     * - pending    = menunggu pembayaran
     * - deny       = ditolak
     * - expire     = kadaluarsa
     * - cancel     = dibatalkan
     */
    private function mapTransactionStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            // Capture hanya untuk kartu kredit
            // Cek fraud status — challenge = perlu review manual
            return $fraudStatus === 'challenge' ? 'pending' : 'success';
        }

        return match ($transactionStatus) {
            'settlement' => 'success',  // Pembayaran berhasil
            'pending'    => 'pending',  // Menunggu pembayaran
            'deny',
            'cancel',
            'expire'     => 'failed',   // Gagal/dibatalkan/expired
            default      => 'pending',
        };
    }
}
