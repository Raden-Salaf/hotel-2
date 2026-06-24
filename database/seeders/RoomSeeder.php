<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // === Buat Kategori Kamar dulu ===
        $standard = RoomCategory::create([
            'name'        => 'Standard',
            'description' => 'Kamar standar dengan fasilitas dasar yang nyaman',
        ]);

        $deluxe = RoomCategory::create([
            'name'        => 'Deluxe',
            'description' => 'Kamar deluxe dengan fasilitas lebih lengkap dan pemandangan taman',
        ]);

        $suite = RoomCategory::create([
            'name'        => 'Suite',
            'description' => 'Kamar suite mewah dengan ruang tamu terpisah dan pemandangan terbaik',
        ]);

        // === Buat Data Kamar ===

        // --- Kamar Standard ---
        $standardRooms = [
            [
                'room_number'    => '101',
                'name'           => 'Standard Twin',
                'description'    => 'Kamar standard dengan 2 tempat tidur single, cocok untuk 2 orang',
                'price_per_night'=> 350000,
                'capacity'       => 2,
                'floor'          => '1',
                'status'         => 'available',
                // Fasilitas disimpan dalam format JSON
                'facilities'     => json_encode(['AC', 'TV', 'WiFi', 'Kamar Mandi Dalam']),
            ],
            [
                'room_number'    => '102',
                'name'           => 'Standard Queen',
                'description'    => 'Kamar standard dengan 1 tempat tidur queen size',
                'price_per_night'=> 400000,
                'capacity'       => 2,
                'floor'          => '1',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'TV', 'WiFi', 'Kamar Mandi Dalam', 'Mini Fridge']),
            ],
            [
                'room_number'    => '103',
                'name'           => 'Standard Twin',
                'description'    => 'Kamar standard dengan 2 tempat tidur single',
                'price_per_night'=> 350000,
                'capacity'       => 2,
                'floor'          => '1',
                'status'         => 'maintenance', // Contoh kamar sedang maintenance
                'facilities'     => json_encode(['AC', 'TV', 'WiFi', 'Kamar Mandi Dalam']),
            ],
        ];

        // Loop dan simpan setiap kamar standard
        foreach ($standardRooms as $room) {
            // Gunakan create() dari relasi agar room_category_id otomatis terisi
            $standard->rooms()->create($room);
        }

        // --- Kamar Deluxe ---
        $deluxeRooms = [
            [
                'room_number'    => '201',
                'name'           => 'Deluxe King',
                'description'    => 'Kamar deluxe dengan tempat tidur king size dan pemandangan taman',
                'price_per_night'=> 650000,
                'capacity'       => 2,
                'floor'          => '2',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'Smart TV', 'WiFi', 'Kamar Mandi Dalam', 'Bathtub', 'Mini Bar']),
            ],
            [
                'room_number'    => '202',
                'name'           => 'Deluxe Twin',
                'description'    => 'Kamar deluxe dengan 2 tempat tidur double',
                'price_per_night'=> 700000,
                'capacity'       => 4,
                'floor'          => '2',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'Smart TV', 'WiFi', 'Kamar Mandi Dalam', 'Bathtub', 'Mini Bar', 'Sofa']),
            ],
            [
                'room_number'    => '203',
                'name'           => 'Deluxe King Garden View',
                'description'    => 'Kamar deluxe dengan pemandangan taman yang indah',
                'price_per_night'=> 750000,
                'capacity'       => 2,
                'floor'          => '2',
                'status'         => 'occupied', // Contoh kamar sedang terisi
                'facilities'     => json_encode(['AC', 'Smart TV', 'WiFi', 'Kamar Mandi Dalam', 'Bathtub', 'Mini Bar', 'Balkon']),
            ],
        ];

        foreach ($deluxeRooms as $room) {
            $deluxe->rooms()->create($room);
        }

        // --- Kamar Suite ---
        $suiteRooms = [
            [
                'room_number'    => '301',
                'name'           => 'Junior Suite',
                'description'    => 'Suite junior dengan ruang tamu kecil dan tempat tidur king size',
                'price_per_night'=> 1200000,
                'capacity'       => 2,
                'floor'          => '3',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'Smart TV', 'WiFi', 'Kamar Mandi Mewah', 'Jacuzzi', 'Mini Bar', 'Ruang Tamu', 'Balkon']),
            ],
            [
                'room_number'    => '302',
                'name'           => 'Senior Suite',
                'description'    => 'Suite premium dengan ruang tamu penuh dan dapur kecil',
                'price_per_night'=> 1800000,
                'capacity'       => 4,
                'floor'          => '3',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'Smart TV', 'WiFi', 'Kamar Mandi Mewah', 'Jacuzzi', 'Mini Bar', 'Ruang Tamu', 'Dapur Kecil', 'Balkon Luas']),
            ],
            [
                'room_number'    => '303',
                'name'           => 'Presidential Suite',
                'description'    => 'Suite terbaik dengan pemandangan 360 derajat dan fasilitas bintang 5',
                'price_per_night'=> 3500000,
                'capacity'       => 6,
                'floor'          => '3',
                'status'         => 'available',
                'facilities'     => json_encode(['AC', 'Smart TV 65"', 'WiFi Premium', 'Kamar Mandi Mewah x2', 'Jacuzzi', 'Bar Pribadi', 'Ruang Tamu', 'Ruang Makan', 'Dapur', 'Balkon Panorama']),
            ],
        ];

        foreach ($suiteRooms as $room) {
            $suite->rooms()->create($room);
        }
    }
}