<?php

namespace App\Http\Controllers\Staff;

use App\Models\Booking;
use App\Models\Device;
use App\Models\Maintenance;
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
    public function dashboard()
    {
        $today = Carbon::today();
        $bookingHariIni = Booking::whereDate('waktu_mulai', $today)->count();
        $bookingAkanDatang = Booking::whereDate('waktu_mulai', $today)
            ->where('status', 'menunggu')
            ->where('waktu_mulai', '>=', now())
            ->count();
        $totalPerangkat = Device::count();
        $sesiAktifCount = Booking::where('status', 'bermain')->count();
        $perangkatSiap = Device::where('status', 'tersedia')->count();
        $perluMaintenance = Device::where('status', 'perbaikan')->count();
        $perangkatMaintenance = Device::where('status', 'perbaikan')->first();

        return view('staff.dashboard', compact(
            'bookingHariIni',
            'bookingAkanDatang',
            'sesiAktifCount',
            'totalPerangkat',
            'perangkatSiap',
            'perluMaintenance',
            'perangkatMaintenance'
        ));
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'device'])
            ->latest('waktu_mulai');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('nama', 'ilike', "%{$search}%");
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
        $sesiAktif = Booking::with(['user', 'device'])
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
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'bermain',
            'status_konfirmasi' => 'dikonfirmasi',
            'id_staff' => Auth::id(),
            'waktu_mulai' => now(),
            'waktu_selesai' => now()->addHours($booking->durasi),
        ]);

        if ($booking->device) {
            $booking->device->update(['status' => 'bermain']);
        }

        return back()->with('success', "Booking {$booking->kode_booking} berhasil Check-in. Sesi dimulai.");
    }

    public function checkOut($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
            'id_staff' => Auth::id(),
        ]);

        if ($booking->device) {
            $booking->device->update(['status' => 'tersedia']);
        }

        return back()->with('success', "Sesi {$booking->kode_booking} telah diselesaikan.");
    }

    public function addDuration($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->durasi += 1;
        $booking->waktu_selesai = Carbon::parse($booking->waktu_selesai)->addHour();
        
        if ($booking->device) {
            $booking->total_harga += $booking->device->harga_per_jam;
        }
        
        $booking->save();

        return back()->with('success', "Durasi {$booking->kode_booking} berhasil ditambah 1 jam.");
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

            $totalHarga = (int) $device->harga_per_jam * $durasi;
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
                'subtotal' => $totalHarga,
                'diskon' => 0,
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

            $totalHarga = (int) $device->harga_per_jam * $durasi;
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
                'subtotal' => $totalHarga,
                'diskon' => 0,
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
                'status_pembayaran' => 'menunggu',
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
            Maintenance::create([
                'id_device' => $device->id,
                'keterangan' => $validated['keterangan'],
                'waktu_mulai' => now(),
                'waktu_selesai' => null,
                'status' => 'berlangsung',
            ]);

            $device->update(['status' => 'perbaikan']);
        });

        return back()->with('success', "Tiket maintenance {$device->nama_device} berhasil dikirim ke admin.");
    }
}
