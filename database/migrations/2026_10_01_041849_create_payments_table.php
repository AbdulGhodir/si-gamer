<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_booking')
                  ->constrained('bookings')
                  ->onDelete('cascade');

            $table->string('kode_transaksi')->unique();

            $table->enum('metode_pembayaran', [
                'qris'
            ])->default('qris');

            $table->bigInteger('jumlah');

            $table->enum('status_pembayaran', [
                'menunggu',
                'berhasil',
                'gagal',
                'kadaluarsa',
                'dibatalkan'
            ])->default('menunggu');

            $table->timestamp('waktu_pembayaran')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
