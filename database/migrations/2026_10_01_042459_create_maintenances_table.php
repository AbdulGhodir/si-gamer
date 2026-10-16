<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_device')
                  ->constrained('devices')
                  ->onDelete('cascade');

            $table->text('keterangan');

            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();

            $table->enum('status', [
                'berlangsung',
                'selesai'
            ])->default('berlangsung');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('maintenances');
    }
};
