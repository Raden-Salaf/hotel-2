<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting! Role dulu, baru user
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            // RoomSeeder & FnBSeeder akan kita tambah setelah model siap
            RoomSeeder::class,  // ← tambahkan ini
            FnBSeeder::class,   // ← dan ini
        ]);
    }
}
