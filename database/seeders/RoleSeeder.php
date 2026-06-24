<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache role & permission dulu
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat semua permission yang ada di sistem
        $permissions = [
            // Room permissions
            'view rooms', 'create rooms', 'edit rooms', 'delete rooms',

            // FnB permissions
            'view fnb', 'create fnb', 'edit fnb', 'delete fnb',
            'manage fnb orders',

            // Booking permissions
            'view bookings', 'create bookings', 'edit bookings',
            'confirm bookings', 'cancel bookings',

            // Invoice & Payment permissions
            'view invoices', 'create invoices',

            // User management (hanya super admin)
            'manage users',

            // Reports
            'view reports',
        ];

        // Buat permission satu per satu
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // === Buat Role dan assign permission ===

        // 1. Super Admin — bisa semua
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Resepsionis — hanya bisa lihat, konfirmasi booking, buat invoice
        $resepsionis = Role::create(['name' => 'resepsionis']);
        $resepsionis->givePermissionTo([
            'view rooms',
            'view bookings', 'create bookings', 'edit bookings',
            'confirm bookings', 'cancel bookings',
            'view invoices', 'create invoices',
        ]);

        // 3. Admin FnB — kelola menu dan terima pesanan
        $adminFnb = Role::create(['name' => 'admin_fnb']);
        $adminFnb->givePermissionTo([
            'view fnb', 'create fnb', 'edit fnb', 'delete fnb',
            'manage fnb orders',
        ]);
    }
}