<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Device;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerBookingController extends Controller
{
    public function booking(Request $request) {
        $allDevices = Device::all();

        $deviceId = $request->query('device', $allDevices->first()?->id);
        $selectedUnit = Device::find($deviceId) ?? $allDevices->first();

        $isMember = Membership::where('id_user', Auth::id())
            ->where('status', 'aktif')
            ->where(function ($query) {
                $query->whereNull('tanggal_berakhir')
                    ->orWhereDate('tanggal_berakhir', '>=', now()->toDateString());
            })
            ->exists();

        return view('customer.booking', compact(
            'selectedUnit',
            'allDevices',
            'isMember'
        ));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'durasi' => 'required'
        ]);

        $durasi = (int)$validate['durasi'];

        $user = Auth::user();
        $membership = Membership::where('id_user', $user->id)->first();

        $device = Device::findOrFail($validate['device_id']);
        $subTotalHarga = $device->harga_per_jam * $durasi;

        $waktuMulai = Carbon::parse($validate['tanggal'] . ' ' . $validate['jam'] . ':00');
        $waktuSelesai = $waktuMulai->copy()->addHours($durasi);

        if ($membership && $membership->status === 'aktif') {
            $totalHarga = $subTotalHarga - ($subTotalHarga * 0.05);
        } else {
            $totalHarga = $subTotalHarga;
        }

        $booking = Booking::create([
            'kode_booking' => 'BK-' . mt_rand(10000, 99999),
            'id_user' => $user->id,
            'id_device' => $device->id,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'durasi' => $durasi,
            'subtotal' => $subTotalHarga,
            'total_harga' => $totalHarga,
            'diskon' => $membership && $membership->status === 'aktif' ? 0.05 : 0,
            'status' => 'menunggu',
            'status_konfirmasi' => 'belum_dikonfirmasi',
            'id_staff' => null,
            'jenis_booking' => 'online',
        ]);

        return redirect()->route('customer.riwayat')->with('success', 'Booking berhasil dibuat!');
    }

    public function riwayat()
    {
        $allBookings = Booking::with(['device', 'staff'])
            ->where('id_user', Auth::id())->latest()
            ->get();

        $allBookings->transform(function ($booking) {
            $tanggalMulai = Carbon::parse($booking->waktu_mulai);

            if ($tanggalMulai->isToday()) {
                $statusHari = 'Hari ini';
            } elseif ($tanggalMulai->isTomorrow()) {
                $statusHari = 'Besok';
            } elseif ($tanggalMulai->isYesterday()) {
                $statusHari = 'Kemarin';
            } else {
                $statusHari = Carbon::parse($booking->waktu_mulai)->format('d M Y');
            }

            $tangalSelesai = Carbon::parse($booking->waktu_selesai);

            if ($tangalSelesai->isSameDay($tanggalMulai)) {
                $waktuSelesai = Carbon::parse($booking->waktu_selesai)->format('H:i');
            } elseif ($tangalSelesai->isToday()) {
                $waktuSelesai = 'Hari ini ' . Carbon::parse($booking->waktu_selesai)->format('H:i');
            } elseif ($tangalSelesai->isTomorrow()) {
                $waktuSelesai = 'Besok, ' . Carbon::parse($booking->waktu_selesai)->format('H:i');
            } elseif ($tangalSelesai->isYesterday()) {
                $waktuSelesai = 'Kemarin, ' . Carbon::parse($booking->waktu_selesai)->format('H:i');
            } else {
                $waktuSelesai = Carbon::parse($booking->waktu_selesai)->format('d M Y H:i');
            }


            $booking->jadwal = $statusHari . ', ' . Carbon::parse($booking->waktu_mulai)->format(' H:i' ) . ' - ' . $waktuSelesai;
            $booking->durasi = $booking->durasi . ' Jam';
            $booking->kode = '#' . $booking->kode_booking;
            $booking->metode = $booking->jenis_booking === 'online' ? 'QRIS' : 'Bayar di Tempat';

            return $booking;
        });

        return view('customer.riwayat', compact(
            'allBookings'
        ));
    }
}
