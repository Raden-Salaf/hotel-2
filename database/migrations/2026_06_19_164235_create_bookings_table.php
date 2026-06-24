<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // Kode unik booking, contoh: BK-20241201-001

            // Relasi ke user (tamu online) — nullable karena walk-in tidak punya akun
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Relasi ke kamar
            $table->foreignId('room_id')->constrained()->onDelete('restrict');

            // Data tamu (untuk walk-in yang tidak punya akun)
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->string('guest_id_card')->nullable(); // KTP/Passport

            // Info booking
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('num_guests');
            $table->text('special_requests')->nullable();

            // Tipe booking: online (via website) atau walk_in (langsung ke resepsionis)
            $table->enum('booking_type', ['online', 'walk_in'])->default('online');

            // Status booking
            $table->enum('status', [
                'pending',      // Baru pesan, belum bayar
                'confirmed',    // Sudah dikonfirmasi (walk-in atau sudah bayar)
                'checked_in',   // Tamu sudah check-in
                'checked_out',  // Tamu sudah check-out
                'cancelled'     // Dibatalkan
            ])->default('pending');

            // Harga
            $table->decimal('room_price', 12, 2);   // Total harga kamar
            $table->decimal('fnb_price', 12, 2)->default(0); // Total harga FnB
            $table->decimal('total_price', 12, 2);  // Grand total

            // Siapa yang menginput (untuk walk-in)
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};