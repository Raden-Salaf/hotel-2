<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('booking_id')->constrained()->onDelete('restrict');

            // Data dari Midtrans
            $table->string('transaction_id')->nullable();   // ID transaksi dari Midtrans
            $table->string('order_id')->unique();           // Order ID yang kita kirim ke Midtrans
            $table->string('payment_type')->nullable();     // bank_transfer, gopay, dll
            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'expired'
            ])->default('pending');

            $table->json('midtrans_response')->nullable(); // Simpan raw response dari Midtrans
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};