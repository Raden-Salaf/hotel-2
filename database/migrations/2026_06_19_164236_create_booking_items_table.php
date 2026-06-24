<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini menyimpan pesanan FnB yang terhubung ke booking
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('fnb_item_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);         // Harga saat dipesan (snapshot)
            $table->decimal('subtotal', 12, 2);
            $table->enum('status', ['pending', 'preparing', 'delivered'])
                  ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};