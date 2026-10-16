<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $table->string('nama_device');
            $table->enum('tipe', ['PS4', 'PS5', 'PC']);
            $table->bigInteger('harga_per_jam');

            $table->enum('status', [
                'tersedia',
                'dipesan',
                'bermain',
                'perbaikan',
                'offline'
            ])->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
