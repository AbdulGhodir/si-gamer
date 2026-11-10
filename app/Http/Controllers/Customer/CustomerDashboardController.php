<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $dummyDevices = [
            [
                'nama_device' => 'PlayStation 5 - A',
                'tipe' => 'PS5',
                'spesifikasi' => 'TV 4K 50 inch • 2 Controller DualSense • Game Terbaru',
                'harga_per_jam' => 25000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 5 - B',
                'tipe' => 'PS5',
                'spesifikasi' => 'TV 4K 50 inch • 2 Controller DualSense • Game Terbaru',
                'harga_per_jam' => 25000,
                'status' => 'penuh',
            ],
            [
                'nama_device' => 'PC Gaming - VIP 01',
                'tipe' => 'PC',
                'spesifikasi' => 'Intel Core i7 Gen 13 • 32GB RAM • RTX 4070 • 240Hz',
                'harga_per_jam' => 20000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 4 - VIP 1',
                'tipe' => 'PS4',
                'spesifikasi' => 'TV FHD 43 inch • 2 Controller DS4 • Sofa Recliner',
                'harga_per_jam' => 15000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PC Gaming - Reguler 01',
                'tipe' => 'PC',
                'spesifikasi' => 'Intel Core i5 Gen 12 • 16GB RAM • RTX 3060 • 165Hz',
                'harga_per_jam' => 12000,
                'status' => 'penuh',
            ],
            [
                'nama_device' => 'PlayStation 4 - Reguler B',
                'tipe' => 'PS4',
                'spesifikasi' => 'TV FHD 40 inch • 2 Controller DS4 • Game Lengkap',
                'harga_per_jam' => 10000,
                'status' => 'maintenance',
            ],
            [
                'nama_device' => 'PC Gaming - Reguler 02',
                'tipe' => 'PC',
                'spesifikasi' => 'Intel Core i5 Gen 12 • 16GB RAM • RTX 3060 • 165Hz',
                'harga_per_jam' => 12000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 5 - VIP 1',
                'tipe' => 'PS5',
                'spesifikasi' => 'TV OLED 4K 55 inch • 2 DualSense Edge • Private Room',
                'harga_per_jam' => 30000,
                'status' => 'maintenance',
            ],
        ];

        return view('customer.dashboard', compact(
            'dummyDevices'
        ));
    }
}