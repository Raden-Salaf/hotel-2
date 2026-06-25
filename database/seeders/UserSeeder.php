<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Super Admin
        $superAdmin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@hotel.com',
            'password' => Hash::make('admin123'),
        ]);
        $superAdmin->assignRole('super_admin');

        // Buat Resepsionis
        $resepsionis = User::create([
            'name'     => 'Bejo Resepsionis',
            'email'    => 'resepsionis@hotel.com',
            'password' => Hash::make('resepsionis123'),
        ]);
        $resepsionis->assignRole('resepsionis');

        // Buat Admin FnB
        $adminFnb = User::create([
            'name'     => 'Siti FnB Admin',
            'email'    => 'fnb@hotel.com',
            'password' => Hash::make('password123'),
        ]);
        $adminFnb->assignRole('admin_fnb');
    }
}
