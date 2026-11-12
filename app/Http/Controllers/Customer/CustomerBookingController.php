<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Device;

class CustomerBookingController extends Controller
{
    public function booking(Request $request) {
        $allDevices = Device::all();

        $deviceId = $request->query('device', $allDevices->first()?->id);
        $selectedUnit = Device::find($deviceId) ?? $allDevices->first();

        return view('customer.booking', compact(
            'selectedUnit',
            'allDevices',
        ));
    }
    

    public function riwayat()
    {
        $riwayatList = [
            [
                'id' => 1,
                'nama_device' => 'PlayStation 5 - A',
                'tipe' => 'PS',
                'status' => 'menunggu',
                'jadwal' => 'Hari ini, 12:00 - 16:00',
                'durasi' => '4 Jam',
                'kode' => '#BK-99210',
                'total' => 60000,
                'customer' => 'Customer',
                'metode' => 'QRIS',
            ],
            [
                'id' => 2,
                'nama_device' => 'PC Reguler - 01',
                'tipe' => 'PC',
                'status' => 'bermain',
                'jadwal' => 'Hari ini, 09:00 - 11:00',
                'durasi' => '2 Jam',
                'kode' => '#BK-99182',
                'total' => 14000,
                'customer' => 'Customer',
                'metode' => 'QRIS',
            ],
            [
                'id' => 3,
                'nama_device' => 'PlayStation 4 - B',
                'tipe' => 'PS',
                'status' => 'selesai',
                'jadwal' => '10 Nov 2026, 19:00 - 21:00',
                'durasi' => '2 Jam',
                'kode' => '#BK-98001',
                'total' => 16000,
                'customer' => 'Customer',
                'metode' => 'QRIS',
            ],
            [
                'id' => 4,
                'nama_device' => 'PC Gaming - VIP 01',
                'tipe' => 'PC',
                'status' => 'selesai',
                'jadwal' => '08 Nov 2026, 14:00 - 17:00',
                'durasi' => '3 Jam',
                'kode' => '#BK-97882',
                'total' => 60000,
                'customer' => 'Customer',
                'metode' => 'QRIS',
            ],
            [
                'id' => 5,
                'nama_device' => 'PlayStation 5 - VIP 1',
                'tipe' => 'PS',
                'status' => 'menunggu',
                'jadwal' => 'Besok, 13:00 - 15:00',
                'durasi' => '2 Jam',
                'kode' => '#BK-99304',
                'total' => 50000,
                'customer' => 'Customer',
                'metode' => 'QRIS',
            ],
        ];

        return view('customer.riwayat', compact(
            'riwayatList'
        ));
    }
}
