<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            [
                'tipe' => 'PS4',
                'harga' => 15000,
                'kategori' => 'Reguler',
                'spesifikasi' => 'PlayStation 4 Slim, Controller DualShock 4, TV LED 32 inch'
            ],
            [
                'tipe' => 'PS5',
                'harga' => 25000,
                'kategori' => 'VIP',
                'spesifikasi' => 'PlayStation 5, DualSense Wireless Controller, TV 4K 43 inch'
            ],
            [
                'tipe' => 'PC',
                'harga' => 20000,
                'kategori' => 'Reguler',
                'spesifikasi' => 'Intel Core i5, RAM 16GB, GTX 1660, Monitor 24 inch'
            ],
        ];

        foreach ($devices as $device) {
            foreach (['A', 'B', 'C', 'D'] as $kode) {

                DB::table('devices')->updateOrInsert(
                    [
                        'nama_device' => match ($device['tipe']) {
                            'PS4' => 'PlayStation 4',
                            'PS5' => 'PlayStation 5',
                            'PC'  => 'PC Gaming',
                        } . ' - ' . $kode
                    ],
                    [
                        'tipe' => $device['tipe'],
                        'harga_per_jam' => $device['harga'],
                        'kategori' => $device['kategori'],
                        'spesifikasi' => $device['spesifikasi'],
                        'status' => 'tersedia',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}