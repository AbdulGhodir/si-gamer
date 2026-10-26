<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pendapatan' => 'Rp18.450.000',
            'transaksi_lunas' => 326,
            'booking_hari_ini' => 24,
            'utilitas' => '62%',
            'operasional_harian' => 'Rp875.000',
        ];

        $user = Auth::user();

        return view('admin.dashboard', compact('stats', 'user'));
    }

    public function users()
    {
        $user = Auth::user();
        
        // Data dummy sementara untuk daftar pengguna
        $users = [
            ['id' => 1, 'nama' => 'Raka Pratama', 'email' => 'raka@gmail.com', 'role' => 'Customer', 'status' => 'Aktif'],
            ['id' => 2, 'nama' => 'Maya Lestari', 'email' => 'maya@gmail.com', 'role' => 'Customer', 'status' => 'Aktif'],
            ['id' => 3, 'nama' => 'Sinta Dewi', 'email' => 'sinta@gmail.com', 'role' => 'Customer', 'status' => 'Pending'],
            ['id' => 4, 'nama' => 'Dina Putri', 'email' => 'dina@gmail.com', 'role' => 'Staff', 'status' => 'Aktif'],
            ['id' => 5, 'nama' => 'Arif Wijaya', 'email' => 'arif@gmail.com', 'role' => 'Admin', 'status' => 'Aktif'],
        ];

        return view('admin.users', compact('users', 'user'));
    }

    public function devices()
    {
        $user = Auth::user();
        $devices = [
            ['kode' => 'PS4-01', 'tipe' => 'PlayStation 4', 'status' => 'Tersedia', 'keterangan' => 'Normal'],
            ['kode' => 'PS4-02', 'tipe' => 'PlayStation 4', 'status' => 'Digunakan', 'keterangan' => 'Sesi aktif (Raka)'],
            ['kode' => 'PS5-01', 'tipe' => 'PlayStation 5', 'status' => 'Tersedia', 'keterangan' => 'Normal'],
            ['kode' => 'PS5-04', 'tipe' => 'PlayStation 5', 'status' => 'Maintenance', 'keterangan' => 'DualSense drift'],
            ['kode' => 'PC-01', 'tipe' => 'PC Gaming', 'status' => 'Digunakan', 'keterangan' => 'Sesi aktif (Maya)'],
        ];
        return view('admin.devices', compact('devices', 'user'));
    }

    public function tariffs()
    {
        $user = Auth::user();
        $tariffs = [
            ['nama' => 'Reguler PlayStation 4', 'kategori' => 'Per Jam', 'harga' => 'Rp15.000', 'status' => 'Aktif'],
            ['nama' => 'Reguler PlayStation 5', 'kategori' => 'Per Jam', 'harga' => 'Rp25.000', 'status' => 'Aktif'],
            ['nama' => 'Reguler PC Gaming', 'kategori' => 'Per Jam', 'harga' => 'Rp20.000', 'status' => 'Aktif'],
            ['nama' => 'Paket Mabar PS5 (3 Jam)', 'kategori' => 'Paket', 'harga' => 'Rp65.000', 'status' => 'Aktif'],
            ['nama' => 'WEEKEND10 (Diskon 10%)', 'kategori' => 'Promo', 'harga' => '10%', 'status' => 'Aktif'],
        ];
        return view('admin.tariffs', compact('tariffs', 'user'));
    }

    public function promos()
    {
        $user = Auth::user();
        $promos = [
            ['kode' => 'WEEKEND10', 'diskon' => '10%', 'ket' => 'Diskon min. Rp50.000', 'status' => 'Aktif'],
            ['kode' => 'MABARPS5', 'diskon' => 'Rp15.000', 'ket' => 'Khusus paket PS5', 'status' => 'Aktif'],
            ['kode' => 'NEWUSER', 'diskon' => '20%', 'ket' => 'Diskon member baru', 'status' => 'Nonaktif'],
        ];
        return view('admin.promos', compact('promos', 'user'));
    }

    public function transactions()
    {
        $user = Auth::user();
        $transactions = [
            ['kode' => 'GH-181026-024', 'pelanggan' => 'Raka Pratama', 'perangkat' => 'PS5-02', 'total' => 'Rp45.000', 'status' => 'Lunas'],
            ['kode' => 'GH-181026-023', 'pelanggan' => 'Maya Lestari', 'perangkat' => 'PC-02', 'total' => 'Rp40.000', 'status' => 'Belum bayar'],
            ['kode' => 'GH-181026-020', 'pelanggan' => 'Sinta Dewi', 'perangkat' => 'PS4-03', 'total' => 'Rp30.000', 'status' => 'Lunas'],
        ];
        return view('admin.transactions', compact('transactions', 'user'));
    }

    public function bookings()
    {
        $user = Auth::user();
        $bookings = [
            ['id' => 'BOK-01', 'pelanggan' => 'Raka Pratama', 'jadwal' => '26/10/2026 18:00', 'durasi' => '2 Jam', 'status' => 'Dikonfirmasi'],
            ['id' => 'BOK-02', 'pelanggan' => 'Maya Lestari', 'jadwal' => '26/10/2026 20:00', 'durasi' => '3 Jam', 'status' => 'Menunggu'],
        ];
        return view('admin.bookings', compact('bookings', 'user'));
    }

    public function maintenance()
    {
        $user = Auth::user();
        $maintenances = [
            ['ticket' => 'MT-026', 'unit' => 'PS5-04', 'masalah' => 'DualSense drift', 'petugas' => 'Dina Putri', 'status' => 'Dalam Penanganan'],
            ['ticket' => 'MT-025', 'unit' => 'PC-01', 'masalah' => 'Update Driver GPU', 'petugas' => 'Arif Wijaya', 'status' => 'Selesai'],
        ];
        return view('admin.maintenance', compact('maintenances', 'user'));
    }

    public function reports()
    {
        $user = Auth::user();
        return view('admin.reports', compact('user'));
    }
}