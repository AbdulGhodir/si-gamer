<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('kode_booking')->unique();

            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('id_device')
                  ->constrained('devices')
                  ->onDelete('cascade');

            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->integer('durasi');
            $table->bigInteger('total_harga');

            $table->enum('status', [
                'menunggu',
                'bermain',
                'selesai',
                'dibatalkan'
            ])->default('menunggu');

            $table->enum('status_konfirmasi', [
                'belum_dikonfirmasi',
                'dikonfirmasi',
            ])->default('belum_dikonfirmasi');

            $table->foreignId('id_staff')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->enum('jenis_booking', [
                'online',
                'langsung'
            ])->default('online');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
