<?php

namespace Database\Seeders;

use App\Models\FnbCategory;
use App\Models\FnbItem;
use Illuminate\Database\Seeder;

class FnBSeeder extends Seeder
{
    public function run(): void
    {
        // === Buat Kategori FnB ===
        $makanan = FnbCategory::create([
            'name' => 'Makanan',
            'icon' => '🍽️',
        ]);

        $minuman = FnbCategory::create([
            'name' => 'Minuman',
            'icon' => '🥤',
        ]);

        $dessert = FnbCategory::create([
            'name' => 'Dessert',
            'icon' => '🍰',
        ]);

        // === Menu Makanan ===
        $menuMakanan = [
            [
                'name'         => 'Nasi Goreng Spesial',
                'description'  => 'Nasi goreng dengan telur, ayam, dan sayuran segar',
                'price'        => 45000,
                'is_available' => true,
            ],
            [
                'name'         => 'Mie Goreng Seafood',
                'description'  => 'Mie goreng dengan udang, cumi, dan sayuran',
                'price'        => 55000,
                'is_available' => true,
            ],
            [
                'name'         => 'Ayam Bakar Madu',
                'description'  => 'Ayam bakar dengan bumbu madu dan rempah pilihan',
                'price'        => 65000,
                'is_available' => true,
            ],
            [
                'name'         => 'Soto Ayam',
                'description'  => 'Soto ayam dengan kuah bening, telur, dan perkedel',
                'price'        => 35000,
                'is_available' => true,
            ],
            [
                'name'         => 'Steak Sirloin',
                'description'  => 'Steak sirloin 200gr dengan saus mushroom dan kentang goreng',
                'price'        => 150000,
                'is_available' => true,
            ],
            [
                'name'         => 'Gado-Gado',
                'description'  => 'Sayuran segar dengan bumbu kacang dan kerupuk',
                'price'        => 30000,
                'is_available' => false, // Contoh menu tidak tersedia
            ],
        ];

        foreach ($menuMakanan as $item) {
            $makanan->items()->create($item);
        }

        // === Menu Minuman ===
        $menuMinuman = [
            [
                'name'         => 'Es Teh Manis',
                'description'  => 'Teh manis segar dengan es batu',
                'price'        => 10000,
                'is_available' => true,
            ],
            [
                'name'         => 'Jus Alpukat',
                'description'  => 'Jus alpukat segar dengan susu dan madu',
                'price'        => 25000,
                'is_available' => true,
            ],
            [
                'name'         => 'Es Kopi Susu',
                'description'  => 'Kopi susu kekinian dengan es batu',
                'price'        => 28000,
                'is_available' => true,
            ],
            [
                'name'         => 'Mineral Water',
                'description'  => 'Air mineral botol 600ml',
                'price'        => 8000,
                'is_available' => true,
            ],
            [
                'name'         => 'Fresh Orange Juice',
                'description'  => 'Jus jeruk segar tanpa tambahan gula',
                'price'        => 30000,
                'is_available' => true,
            ],
            [
                'name'         => 'Mocktail Tropical',
                'description'  => 'Minuman segar campuran buah tropis',
                'price'        => 35000,
                'is_available' => true,
            ],
        ];

        foreach ($menuMinuman as $item) {
            $minuman->items()->create($item);
        }

        // === Menu Dessert ===
        $menuDessert = [
            [
                'name'         => 'Es Krim Vanilla',
                'description'  => 'Es krim vanilla premium 2 scoop',
                'price'        => 25000,
                'is_available' => true,
            ],
            [
                'name'         => 'Pudding Coklat',
                'description'  => 'Pudding coklat lembut dengan saus karamel',
                'price'        => 20000,
                'is_available' => true,
            ],
            [
                'name'         => 'Pancake Madu',
                'description'  => 'Pancake fluffy dengan madu dan buah segar',
                'price'        => 35000,
                'is_available' => true,
            ],
            [
                'name'         => 'Pisang Goreng Keju',
                'description'  => 'Pisang goreng crispy dengan topping keju dan coklat',
                'price'        => 22000,
                'is_available' => true,
            ],
        ];

        foreach ($menuDessert as $item) {
            $dessert->items()->create($item);
        }
    }
}