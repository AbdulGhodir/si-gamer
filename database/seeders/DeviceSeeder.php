<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            ['PS4', 15000, 'PlayStation 4'],
            ['PS5', 25000, 'PlayStation 5'],
            ['PC', 20000, 'PC Gaming'],
        ];

        foreach ($devices as [$tipe, $harga, $nama]) {
            for ($i = 1; $i <= 4; $i++) {
                DB::table('devices')->updateOrInsert(
                    ['nama_device' => $nama . ' ' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                    [
                        'tipe' => $tipe,
                        'harga_per_jam' => $harga,
                        'status' => 'tersedia',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}