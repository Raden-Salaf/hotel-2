<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke booking tamu
            $table->foreignId('booking_id')
                ->constrained()
                ->onDelete('cascade');

            // Relasi ke item laundry
            $table->foreignId('laundry_item_id')
                ->constrained()
                ->onDelete('restrict');

            $table->integer('quantity');
            $table->decimal('price', 12, 2);     // Harga saat dipesan (snapshot)
            $table->decimal('subtotal', 12, 2);

            // Status alur laundry
            $table->enum('status', ['pending', 'processing', 'done'])
                ->default('pending');

            // Catatan khusus (misal: hati-hati warna, dll)
            $table->text('notes')->nullable();

            // Siapa yang input pesanan
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
