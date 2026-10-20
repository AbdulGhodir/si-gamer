<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $devices = DB::table('devices')
            ->select(
                'tipe',
                DB::raw("MIN(id) FILTER (WHERE status = 'tersedia') AS id"),
                DB::raw('MIN(nama_device) AS nama_device'),
                DB::raw('MIN(harga_per_jam) AS harga_per_jam'),
                DB::raw("COUNT(*) FILTER (WHERE status = 'tersedia') AS tersedia"),
                DB::raw('COUNT(*) AS jumlah')
            )
            ->whereIn('tipe', ['PS4', 'PS5', 'PC'])
            ->groupBy('tipe')
            ->orderByRaw("CASE tipe WHEN 'PS4' THEN 1 WHEN 'PS5' THEN 2 ELSE 3 END")
            ->get();

        $bookings = DB::table('bookings')
            ->join('devices', 'bookings.id_device', '=', 'devices.id')
            ->leftJoin('payments', 'bookings.id', '=', 'payments.id_booking')
            ->where('bookings.id_user', $user->id)
            ->select(
                'bookings.*',
                'devices.nama_device',
                'devices.tipe',
                'payments.kode_transaksi',
                'payments.status_pembayaran'
            )
            ->orderByDesc('bookings.created_at')
            ->get();

        $bookingSaya = $bookings;
        $riwayat = $bookings->take(8);

        $membership = DB::table('memberships')
            ->where('id_user', $user->id)
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_berakhir', '>=', now()->toDateString())
            ->orderByDesc('tanggal_berakhir')
            ->first();

        $membershipAktif = $membership !== null;
        $tanggalBerakhirMember = $membership?->tanggal_berakhir;

        return view('customer.dashboard', compact(
            'user',
            'devices',
            'bookings',
            'bookingSaya',
            'riwayat',
            'membershipAktif',
            'tanggalBerakhirMember'
        ));
    }

    public function aktifkanMembership()
    {
        $userId = Auth::id();
        $hariIni = now()->toDateString();

        $membershipAktif = DB::table('memberships')
            ->where('id_user', $userId)
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', $hariIni)
            ->where('tanggal_berakhir', '>=', $hariIni)
            ->exists();

        if ($membershipAktif) {
            return redirect()
                ->route('customer.dashboard')
                ->with('success', 'Membership kamu masih aktif.');
        }

        DB::table('memberships')->insert([
            'id_user' => $userId,
            'tanggal_mulai' => $hariIni,
            'tanggal_berakhir' => now()->addDays(29)->toDateString(),
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Membership berhasil diaktifkan selama 30 hari!');
    }

    private function membershipAktif(int $userId): bool
    {
        return DB::table('memberships')
            ->where('id_user', $userId)
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_berakhir', '>=', now()->toDateString())
            ->exists();
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'id_device' => ['required', 'integer', 'exists:devices,id'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'integer', 'min:0', 'max:23'],
            'durasi' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $jamMulai = (int) $data['jam_mulai'];
        $durasi = (int) $data['durasi'];

        if ($jamMulai + $durasi > 24) {
            throw ValidationException::withMessages([
                'durasi' => 'Jam selesai tidak boleh melewati pukul 24.00.',
            ]);
        }

        $mulai = Carbon::parse($data['tanggal'])
            ->startOfDay()
            ->addHours($jamMulai);

        $selesai = $mulai->copy()->addHours($durasi);

        if ($mulai->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jam mulai harus berada di masa depan.',
            ]);
        }

        DB::transaction(function () use ($data, $mulai, $selesai, $durasi) {
            $device = DB::table('devices')
                ->where('id', $data['id_device'])
                ->lockForUpdate()
                ->first();

            if (!$device || $device->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat sedang tidak tersedia.',
                ]);
            }

            $bentrok = DB::table('bookings')
                ->where('id_device', $device->id)
                ->whereIn('status', ['menunggu', 'bermain'])
                ->where('waktu_mulai', '<', $selesai)
                ->where('waktu_selesai', '>', $mulai)
                ->exists();

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal tersebut sudah dipesan. Pilih jam lain.',
                ]);
            }

            $subtotal = (int) $device->harga_per_jam * $durasi;

            $memberAktif = $this->membershipAktif(Auth::id());
            $diskon = $memberAktif ? (int) round($subtotal * 0.10) : 0;
            $total = $subtotal - $diskon;

            $bookingId = DB::table('bookings')->insertGetId([
                'kode_booking' => 'GH-' . strtoupper(Str::random(8)),
                'id_user' => Auth::id(),
                'id_device' => $device->id,
                'waktu_mulai' => $mulai,
                'waktu_selesai' => $selesai,
                'durasi' => $durasi,
                'total_harga' => $total,
                'status' => 'menunggu',
                'status_konfirmasi' => 'belum_dikonfirmasi',
                'jenis_booking' => 'online',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payments')->insert([
                'id_booking' => $bookingId,
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
                'metode_pembayaran' => 'qris',
                'jumlah' => $total,
                'status_pembayaran' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan pembayaran kepada petugas.');
    }
}