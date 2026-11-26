<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Device;
use App\Models\Membership;
use App\Models\Payment;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Midtrans\Snap;

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
            'durasi' => 'required|integer|min:1|max:12',
        ]);

        $durasi = (int)$validate['durasi'];

        $user = Auth::user();
        $membership = Membership::where('id_user', $user->id)
            ->where('status', 'aktif')
            ->where(function ($query) {
                $query->whereNull('tanggal_berakhir')
                    ->orWhereDate('tanggal_berakhir', '>=', today());
            })
            ->first();

        $device = Device::findOrFail($validate['device_id']);

        $waktuMulai = Carbon::parse($validate['tanggal'] . ' ' . $validate['jam'] . ':00');

        if ($waktuMulai->isPast()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Waktu mulai tidak boleh di masa lalu.']);
        }

        $waktuSelesai = $waktuMulai->copy()->addHours($durasi);
        $result = DB::transaction(function () use ($user, $device, $waktuMulai, $waktuSelesai, $durasi, $membership) {
            $lockedDevice = Device::query()->lockForUpdate()->findOrFail($device->id);

            if (in_array($lockedDevice->status, ['perbaikan', 'offline'], true)) {
                throw ValidationException::withMessages([
                    'device_id' => 'Perangkat sedang tidak tersedia untuk booking.',
                ]);
            }

            if (Booking::hasScheduleConflict($lockedDevice->id, $waktuMulai, $waktuSelesai)) {
                throw ValidationException::withMessages([
                    'jam' => 'Jadwal perangkat berbenturan dengan booking lain. Silakan pilih waktu lain.',
                ]);
            }

            $subTotalHarga = (int) $lockedDevice->harga_per_jam * $durasi;
            $diskon = $membership ? (int) round($subTotalHarga * 0.05) : 0;
            $totalHarga = $subTotalHarga - $diskon;
            $kodeBooking = 'BK-' . mt_rand(10000, 99999);

            while (Booking::where('kode_booking', $kodeBooking)->exists()) {
                $kodeBooking = 'BK-' . mt_rand(10000, 99999);
            }

            $booking = Booking::create([
                'kode_booking' => $kodeBooking,
                'id_user' => $user->id,
                'id_device' => $lockedDevice->id,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'durasi' => $durasi,
                'subtotal' => $subTotalHarga,
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'status' => 'menunggu',
                'status_konfirmasi' => 'belum_dikonfirmasi',
                'id_staff' => null,
                'jenis_booking' => 'online',
            ]);

            $payment = Payment::create([
                'kode_transaksi' => 'TR-' . $booking->id . mt_rand(10000, 99999),
                'id_booking' => $booking->id,
                'metode_pembayaran' => 'qris',
                'jumlah' => $totalHarga,
                'status_pembayaran' => 'menunggu',
            ]);

            return $payment;
        });

        app(MidtransService::class);

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $result->kode_transaksi,
                'gross_amount' => (int) $result->jumlah,
            ],

            'customer_details' => [
                'first_name' => $user->nama,
                'email' => $user->email,
            ],

            'enabled_payments' => ['other_qris'],
        ]);

        $result->update([
            'snap_token' => $snapToken,
        ]);


        return redirect()
            ->route('customer.payment.checkout', $result->id)
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }

    public function riwayat()
    {
        $allBookings = Booking::with(['device', 'staff', 'payment'])
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
