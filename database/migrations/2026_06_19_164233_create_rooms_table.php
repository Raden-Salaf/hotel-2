<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_category_id')
                  ->constrained()                  // otomatis reference ke room_categories.id
                  ->onDelete('restrict');           // tidak bisa hapus kategori kalau masih ada kamar
            $table->string('room_number')->unique(); // Nomor kamar unik, contoh: 101, 202
            $table->string('name');                  // Nama kamar, contoh: Deluxe King
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 12, 2); // Harga per malam
            $table->integer('capacity');               // Kapasitas tamu
            $table->string('floor')->nullable();        // Lantai berapa
            $table->enum('status', ['available', 'occupied', 'maintenance'])
                  ->default('available');
            $table->string('image')->nullable();        // Path foto kamar
            $table->json('facilities')->nullable();     // Fasilitas dalam format JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
