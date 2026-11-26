<?php

namespace App\Http\Controllers\Staff;

use App\Models\Booking;
use App\Models\Device;
use App\Models\Maintenance;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    private function calculatePricing(int $userId, int $hargaPerJam, int $durasi): array
    {
        $hasActiveMembership = Membership::where('id_user', $userId)
            ->where('status', 'aktif')
            ->where(function ($query) {
                $query->whereNull('tanggal_berakhir')
                    ->orWhereDate('tanggal_berakhir', '>=', today());
            })
            ->exists();

        $subtotal = $hargaPerJam * $durasi;
        $diskon = $hasActiveMembership ? (int) round($subtotal * 0.05) : 0;
        $totalHarga = max(0, $subtotal - $diskon);

        return [$subtotal, $diskon, $totalHarga];
    }

    public function dashboard()
    {
        $today = Carbon::today();
        $bookingHariIni = Booking::whereDate('waktu_mulai', $today)
            ->where('status', '!=', 'dibatalkan')
            ->count();
        $bookingAkanDatang = Booking::whereDate('waktu_mulai', $today)
            ->where('status', 'menunggu')
            ->where('waktu_mulai', '>=', now())
            ->count();
        $totalPerangkat = Device::count();
        $sesiAktifCount = Booking::where('status', 'bermain')->count();
        $perangkatSiap = Device::where('status', 'tersedia')->count();
        $perluMaintenance = Device::where('status', 'perbaikan')->count();
        $perangkatMaintenance = Device::where('status', 'perbaikan')->orderBy('nama_device')->first();
        $agendaBooking = Booking::with(['user', 'device'])
            ->where('status', 'menunggu')
            ->where('waktu_mulai', '>=', now())
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'bookingHariIni',
            'bookingAkanDatang',
            'sesiAktifCount',
            'totalPerangkat',
            'perangkatSiap',
            'perluMaintenance',
            'perangkatMaintenance',
            'agendaBooking'
        ));
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'device', 'payment'])
            ->latest('waktu_mulai');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $like = '%' . mb_strtolower($search) . '%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(kode_booking) LIKE ?', [$like])
                    ->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery->whereRaw('LOWER(nama) LIKE ?', [$like]);
                    });
            });
        }

        if (in_array($request->query('status'), ['menunggu', 'bermain', 'selesai', 'dibatalkan'], true)) {
            $query->where('status', $request->query('status'));
        }

        $daftarBooking = $query->paginate(10)->withQueryString();
        $customers = User::where('role', 'customer')->orderBy('nama')->get();
        $devicesBookable = Device::whereNotIn('status', ['perbaikan', 'offline'])
            ->orderBy('nama_device')
            ->get();

        return view('staff.bookings', compact('daftarBooking', 'customers', 'devicesBookable'));
    }

    public function sessions()
    {
        $sesiAktif = Booking::with(['user', 'device', 'payment'])
            ->where('status', 'bermain')
            ->orderBy('waktu_selesai')
            ->get();

        return view('staff.sessions', compact('sesiAktif'));
    }

    public function walkIn()
    {
        $customers = User::where('role', 'customer')->orderBy('nama')->get();
        $devicesTersedia = Device::where('status', 'tersedia')->orderBy('nama_device')->get();

        return view('staff.walk-in', compact('customers', 'devicesTersedia'));
    }

    public function devices()
    {
        $devices = Device::orderBy('tipe')->orderBy('nama_device')->get();

        return view('staff.devices', compact('devices'));
    }

    public function maintenance()
    {
        $maintenances = Maintenance::with('device')->latest('id')->get();
        $deviceIdsWithActiveMaintenance = Maintenance::where('status', 'berlangsung')
            ->select('id_device');
        $devices = Device::whereNotIn('id', $deviceIdsWithActiveMaintenance)
            ->orderBy('nama_device')
            ->get();

        return view('staff.maintenance', compact('maintenances', 'devices'));
    }

    public function checkIn($id)
    {
        $booking = DB::transaction(function () use ($id) {
            $booking = Booking::with(['device', 'payment'])->lockForUpdate()->findOrFail($id);

            if ($booking->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'booking' => 'Hanya booking berstatus menunggu yang dapat melakukan check-in.',
                ]);
            }

            $device = Device::query()->lockForUpdate()->findOrFail($booking->id_device);

            if (in_array($device->status, ['perbaikan', 'offline'], true)) {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat sedang dalam perbaikan atau offline, tidak dapat check-in.',
                ]);
            }

            $hasActiveSession = Booking::where('id_device', $device->id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'bermain')
                ->exists();

            if ($device->status === 'bermain' || $hasActiveSession) {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat sedang digunakan oleh sesi aktif lain.',
                ]);
            }

            $waktuMulai = now();
            $waktuSelesai = $waktuMulai->copy()->addHours((int) $booking->durasi);

            $booking->update([
                'status' => 'bermain',
                'status_konfirmasi' => 'dikonfirmasi',
                'id_staff' => Auth::id(),
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
            ]);

            if (!$booking->payment) {
                Payment::create([
                    'id_booking' => $booking->id,
                    'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                    'metode_pembayaran' => 'qris',
                    'jumlah' => (int) ($booking->total_harga ?? $booking->subtotal ?? 0),
                    'status_pembayaran' => 'menunggu',
                ]);
            }

            $device->update(['status' => 'bermain']);

            return $booking;
        });

        return back()->with('success', "Booking {$booking->kode_booking} berhasil Check-in. Sesi dimulai.");
    }

    public function checkOut($id)
    {
        $booking = DB::transaction(function () use ($id) {
            $booking = Booking::with(['device', 'payment'])->lockForUpdate()->findOrFail($id);

            if ($booking->status !== 'bermain') {
                throw ValidationException::withMessages([
                    'booking' => 'Hanya sesi yang sedang bermain yang dapat di-check-out.',
                ]);
            }

            $now = now();

            $booking->update([
                'status' => 'selesai',
                'waktu_selesai' => $now,
                'id_staff' => Auth::id(),
            ]);

            $nominal = (int) ($booking->total_harga ?? $booking->subtotal ?? 0);

            if (!$booking->payment) {
                Payment::create([
                    'id_booking' => $booking->id,
                    'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                    'metode_pembayaran' => 'qris',
                    'jumlah' => $nominal,
                    'status_pembayaran' => 'berhasil',
                    'waktu_pembayaran' => $now,
                ]);
            } elseif ($booking->payment->status_pembayaran === 'menunggu') {
                $booking->payment->update([
                    'jumlah' => $nominal,
                    'status_pembayaran' => 'berhasil',
                    'waktu_pembayaran' => $now,
                ]);
            }

            if ($booking->device) {
                $device = Device::query()->lockForUpdate()->find($booking->id_device);
                if ($device) {
                    $hasActiveMaintenance = Maintenance::where('id_device', $device->id)
                        ->where('status', 'berlangsung')
                        ->exists();
                    $hasOtherActiveSession = Booking::where('id_device', $device->id)
                        ->where('id', '!=', $booking->id)
                        ->where('status', 'bermain')
                        ->exists();

                    $nextStatus = $hasActiveMaintenance
                        ? 'perbaikan'
                        : ($hasOtherActiveSession ? 'bermain' : 'tersedia');

                    $device->update(['status' => $nextStatus]);
                }
            }

            return $booking;
        });

        return back()->with('success', "Sesi {$booking->kode_booking} telah diselesaikan.");
    }

    public function addDuration($id)
    {
        $booking = DB::transaction(function () use ($id) {
            $booking = Booking::with(['device', 'payment'])->lockForUpdate()->findOrFail($id);

            if ($booking->status !== 'bermain') {
                throw ValidationException::withMessages([
                    'booking' => 'Durasi hanya dapat ditambah untuk sesi yang sedang bermain.',
                ]);
            }

            $currentEnd = Carbon::parse($booking->waktu_selesai);
            $newEnd = $currentEnd->copy()->addHour();

            $hasConflict = Booking::query()
                ->where('id_device', $booking->id_device)
                ->where('id', '!=', $booking->id)
                ->whereNotIn('status', ['dibatalkan', 'selesai'])
                ->where('waktu_mulai', '<', $newEnd)
                ->where('waktu_selesai', '>', $currentEnd)
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'durasi' => 'Tidak dapat menambah durasi karena bentrok dengan jadwal booking berikutnya.',
                ]);
            }

            $hargaPerJam = (int) ($booking->device->harga_per_jam ?? 0);
            $newDurasi = (int) $booking->durasi + 1;
            [$newSubtotal, $newDiskon, $newTotalHarga] = $this->calculatePricing((int) $booking->id_user, $hargaPerJam, $newDurasi);

            $booking->update([
                'durasi' => $newDurasi,
                'waktu_selesai' => $newEnd,
                'subtotal' => $newSubtotal,
                'diskon' => $newDiskon,
                'total_harga' => $newTotalHarga,
                'id_staff' => Auth::id(),
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'jumlah' => $newTotalHarga,
                ]);
            } else {
                Payment::create([
                    'id_booking' => $booking->id,
                    'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                    'metode_pembayaran' => 'qris',
                    'jumlah' => $newTotalHarga,
                    'status_pembayaran' => 'menunggu',
                ]);
            }

            return $booking;
        });

        return back()->with('success', "Durasi {$booking->kode_booking} berhasil ditambah 1 jam.");
    }

    public function confirmPayment($id)
    {
        $booking = DB::transaction(function () use ($id) {
            $booking = Booking::with('payment')->lockForUpdate()->findOrFail($id);

            if ($booking->status === 'dibatalkan') {
                throw ValidationException::withMessages([
                    'payment' => 'Booking yang dibatalkan tidak dapat dikonfirmasi pembayarannya.',
                ]);
            }

            $nominal = (int) ($booking->total_harga ?? $booking->subtotal ?? 0);

            if (!$booking->payment) {
                Payment::create([
                    'id_booking' => $booking->id,
                    'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                    'metode_pembayaran' => 'qris',
                    'jumlah' => $nominal,
                    'status_pembayaran' => 'berhasil',
                    'waktu_pembayaran' => now(),
                ]);
            } else {
                $booking->payment->update([
                    'jumlah' => $nominal,
                    'status_pembayaran' => 'berhasil',
                    'waktu_pembayaran' => now(),
                ]);
            }

            if ($booking->status_konfirmasi !== 'dikonfirmasi') {
                $booking->update([
                    'status_konfirmasi' => 'dikonfirmasi',
                    'id_staff' => Auth::id(),
                ]);
            }

            return $booking;
        });

        return back()->with('success', "Pembayaran tunai booking {$booking->kode_booking} berhasil dikonfirmasi lunas.");
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'id_user' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'customer')),
            ],
            'id_device' => 'required|exists:devices,id',
            'waktu_mulai' => 'required|date|after_or_equal:now',
            'durasi' => 'required|integer|min:1|max:24',
        ]);

        $waktuMulai = Carbon::parse($validated['waktu_mulai']);
        $durasi = (int) $validated['durasi'];
        $waktuSelesai = $waktuMulai->copy()->addHours($durasi);

        $booking = DB::transaction(function () use ($validated, $waktuMulai, $waktuSelesai, $durasi) {
            $device = Device::query()->lockForUpdate()->findOrFail($validated['id_device']);

            if (in_array($device->status, ['perbaikan', 'offline'], true)) {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat sedang tidak tersedia untuk booking.',
                ]);
            }

            if (Booking::hasScheduleConflict($device->id, $waktuMulai, $waktuSelesai)) {
                throw ValidationException::withMessages([
                    'waktu_mulai' => 'Jadwal perangkat berbenturan dengan booking lain.',
                ]);
            }

            [$subtotal, $diskon, $totalHarga] = $this->calculatePricing((int) $validated['id_user'], (int) $device->harga_per_jam, $durasi);
            $kodeBooking = 'STF-' . strtoupper(Str::random(8));

            while (Booking::where('kode_booking', $kodeBooking)->exists()) {
                $kodeBooking = 'STF-' . strtoupper(Str::random(8));
            }

            $booking = Booking::create([
                'kode_booking' => $kodeBooking,
                'id_user' => $validated['id_user'],
                'id_device' => $device->id,
                'id_staff' => Auth::id(),
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'durasi' => $durasi,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_harga' => $totalHarga,
                'status' => 'menunggu',
                'status_konfirmasi' => 'dikonfirmasi',
                'jenis_booking' => 'langsung',
            ]);

            Payment::create([
                'id_booking' => $booking->id,
                'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                'metode_pembayaran' => 'qris',
                'jumlah' => $totalHarga,
                'status_pembayaran' => 'menunggu',
            ]);

            return $booking;
        });

        return back()->with('success', "Booking {$booking->kode_booking} berhasil dibuat.");
    }

    public function storeWalkIn(Request $request)
    {
        $validated = $request->validate([
            'id_user' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'customer')),
            ],
            'id_device' => 'required|exists:devices,id',
            'durasi' => 'required|integer|min:1|max:12',
        ]);

        $durasi = (int) $validated['durasi'];
        $waktuMulai = now();
        $waktuSelesai = $waktuMulai->copy()->addHours($durasi);

        $booking = DB::transaction(function () use ($validated, $durasi, $waktuMulai, $waktuSelesai) {
            $device = Device::query()->lockForUpdate()->findOrFail($validated['id_device']);

            if ($device->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat tidak sedang tersedia.',
                ]);
            }

            if (Booking::hasScheduleConflict($device->id, $waktuMulai, $waktuSelesai)) {
                throw ValidationException::withMessages([
                    'id_device' => 'Perangkat sudah memiliki booking pada waktu tersebut.',
                ]);
            }

            [$subtotal, $diskon, $totalHarga] = $this->calculatePricing((int) $validated['id_user'], (int) $device->harga_per_jam, $durasi);
            $kodeBooking = 'GH-' . now()->format('dmy') . '-' . strtoupper(Str::random(6));

            while (Booking::where('kode_booking', $kodeBooking)->exists()) {
                $kodeBooking = 'GH-' . now()->format('dmy') . '-' . strtoupper(Str::random(6));
            }

            $booking = Booking::create([
                'kode_booking' => $kodeBooking,
                'id_user' => $validated['id_user'],
                'id_device' => $device->id,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'durasi' => $durasi,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_harga' => $totalHarga,
                'status' => 'bermain',
                'status_konfirmasi' => 'dikonfirmasi',
                'id_staff' => Auth::id(),
                'jenis_booking' => 'langsung',
            ]);

            Payment::create([
                'id_booking' => $booking->id,
                'kode_transaksi' => 'TR-' . $booking->id . strtoupper(Str::random(8)),
                'metode_pembayaran' => 'qris',
                'jumlah' => $totalHarga,
                'status_pembayaran' => 'berhasil',
                'waktu_pembayaran' => $waktuMulai,
            ]);

            $device->update(['status' => 'bermain']);

            return $booking;
        });

        return back()->with('success', "Booking walk-in {$booking->kode_booking} berhasil dibuat dan dimulai!");
    }

    public function storeMaintenance(Request $request)
    {
        $validated = $request->validate([
            'id_device' => 'required|exists:devices,id',
            'keterangan' => 'required|string|max:1000',
        ]);

        $device = Device::findOrFail($validated['id_device']);

        if (Maintenance::where('id_device', $device->id)->where('status', 'berlangsung')->exists()) {
            return back()
                ->withErrors(['id_device' => 'Perangkat ini sudah memiliki tiket maintenance yang sedang ditangani.'])
                ->withInput();
        }

        DB::transaction(function () use ($device, $validated) {
            $lockedDevice = Device::query()->lockForUpdate()->findOrFail($device->id);

            Maintenance::create([
                'id_device' => $lockedDevice->id,
                'keterangan' => $validated['keterangan'],
                'waktu_mulai' => now(),
                'waktu_selesai' => null,
                'status' => 'berlangsung',
            ]);

            $lockedDevice->update(['status' => 'perbaikan']);
        });

        return back()->with('success', "Tiket maintenance {$device->nama_device} berhasil dikirim ke admin.");
    }

    public function completeMaintenance($id)
    {
        $maintenance = DB::transaction(function () use ($id) {
            $maintenance = Maintenance::with('device')->lockForUpdate()->findOrFail($id);

            if ($maintenance->status !== 'selesai') {
                $maintenance->update([
                    'status' => 'selesai',
                    'waktu_selesai' => now(),
                ]);

                if ($maintenance->device) {
                    $device = Device::query()->lockForUpdate()->find($maintenance->id_device);
                    if ($device) {
                        $hasOtherMaintenance = Maintenance::where('id_device', $device->id)
                            ->where('id', '!=', $maintenance->id)
                            ->where('status', 'berlangsung')
                            ->exists();
                        $hasActiveSession = Booking::where('id_device', $device->id)
                            ->where('status', 'bermain')
                            ->exists();

                        $nextStatus = $hasOtherMaintenance
                            ? 'perbaikan'
                            : ($hasActiveSession ? 'bermain' : 'tersedia');

                        $device->update(['status' => $nextStatus]);
                    }
                }
            }

            return $maintenance;
        });

        $namaDevice = $maintenance->device->nama_device ?? 'perangkat';

        return back()->with('success', "Perbaikan {$namaDevice} telah diselesaikan dan perangkat kembali tersedia.");
    }
}
