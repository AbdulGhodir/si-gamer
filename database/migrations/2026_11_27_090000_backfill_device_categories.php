<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('devices')
            ->whereNull('kategori')
            ->whereIn('tipe', ['PC', 'PS4'])
            ->update([
                'kategori' => 'Reguler',
                'updated_at' => now(),
            ]);

        DB::table('devices')
            ->whereNull('kategori')
            ->where('tipe', 'PS5')
            ->update([
                'kategori' => 'VIP',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Keep the backfilled categories; reverting them would erase usable device data.
    }
};
