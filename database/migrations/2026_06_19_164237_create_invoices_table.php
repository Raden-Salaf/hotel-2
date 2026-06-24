<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Contoh: INV-20241201-001
            $table->foreignId('booking_id')->constrained()->onDelete('restrict');

            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);     // Pajak (misal 11%)
            $table->decimal('discount', 12, 2)->default(0); // Diskon jika ada
            $table->decimal('total', 12, 2);

            // Status invoice mengikuti status pembayaran
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');

            $table->date('due_date')->nullable();    // Batas waktu pembayaran
            $table->date('paid_at')->nullable();     // Kapan dibayar
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};