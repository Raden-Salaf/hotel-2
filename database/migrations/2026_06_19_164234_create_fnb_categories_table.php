<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Makanan, Minuman, Dessert
            $table->string('icon')->nullable(); // Icon untuk tampilan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_categories');
    }
};