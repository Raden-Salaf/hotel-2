<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fnb_category_id')
                  ->constrained()
                  ->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true); // Apakah menu tersedia hari ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_items');
    }
};