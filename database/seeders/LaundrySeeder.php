<?php

namespace Database\Seeders;

use App\Models\LaundryItem;
use Illuminate\Database\Seeder;

class LaundrySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Baju',    'price' => 5000,  'unit' => 'pcs', 'icon' => '👕'],
            ['name' => 'Celana',  'price' => 7000,  'unit' => 'pcs', 'icon' => '👖'],
            ['name' => 'Jaket',   'price' => 10000, 'unit' => 'pcs', 'icon' => '🧥'],
            ['name' => 'Kaos',    'price' => 4000,  'unit' => 'pcs', 'icon' => '👔'],
            ['name' => 'Sepatu',  'price' => 15000, 'unit' => 'pcs', 'icon' => '👟'],
            ['name' => 'Selimut', 'price' => 20000, 'unit' => 'pcs', 'icon' => '🛏️'],
            ['name' => 'Pakaian', 'price' => 8000,  'unit' => 'kg',  'icon' => '🧺'],
        ];

        foreach ($items as $item) {
            LaundryItem::create([
                ...$item,
                'is_available' => true,
            ]);
        }
    }
}