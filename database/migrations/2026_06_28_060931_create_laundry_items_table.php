<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Contoh: Baju, Celana, Jaket
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);     // Harga per item/kg
            $table->string('unit')->default('pcs'); // pcs atau kg
            $table->boolean('is_available')->default(true);
            $table->string('icon')->nullable();  // Emoji icon
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_items');
    }
};
