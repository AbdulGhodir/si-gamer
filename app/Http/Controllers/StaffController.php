<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Device;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();

        // 1. Kartu Statistik Atas
        $bookingHariIni = Booking::whereDate('waktu_mulai', $today)->count();
        $bookingAkanDatang = Booking::whereDate('waktu_mulai', $today)
            ->where('status', 'menunggu')
            ->where('waktu_mulai', '>=', now())
            ->count();

        $totalPerangkat = Device::count();
        $sesiAktifCount = Booking::where('status', 'bermain')->count();
        $perangkatSiap = Device::where('status', 'tersedia')->count();
        $perluMaintenance = Device::where('status', 'perbaikan')->count();

        // 2. Daftar Booking Hari Ini (Search & Filter Status)
        $queryBooking = Booking::with(['user', 'device'])
            ->whereDate('waktu_mulai', $today)
            ->latest('waktu_mulai');

        if ($request->filled('search')) {
            $search = $request->search;
            $queryBooking->where(function ($q) use ($search) {
                $q->where('kode_booking', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('nama', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $queryBooking->where('status', $request->status);
        }

        $daftarBooking = $queryBooking->paginate(5)->withQueryString();
        $totalSelesaiHariIni = Booking::whereDate('waktu_mulai', $today)->where('status', 'selesai')->count();

        // 3. Sesi Aktif
        $sesiAktif = Booking::with(['user', 'device'])
            ->where('status', 'bermain')
            ->orderBy('waktu_selesai', 'asc')
            ->get();

        // 4. Status Seluruh Perangkat
        $devices = Device::orderBy('nama_device')->get()->groupBy('tipe');

        // 5. Laporan Perangkat Perbaikan
        $perangkatMaintenance = Device::where('status', 'perbaikan')->first();

        // Data untuk Dropdown Modal Walk-in
        $devicesTersedia = Device::where('status', 'tersedia')->get();

        return view('staff.dashboard', compact(
            'bookingHariIni',
            'bookingAkanDatang',
            'sesiAktifCount',
            'totalPerangkat',
            'perangkatSiap',
            'perluMaintenance',
            'daftarBooking',
            'totalSelesaiHariIni',
            'sesiAktif',
            'devices',
            'perangkatMaintenance',
            'devicesTersedia'
        ));
    }

    // Check-in Pelanggan (Mulai Bermain)
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

    // Check-out Pelanggan (Selesai Bermain)
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

    // Tambah Durasi (+1 Jam)
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

    // Booking Langsung / Walk-in
    public function storeWalkIn(Request $request)
    {
        $request->validate([
            'id_device' => 'required|exists:devices,id',
            'durasi' => 'required|integer|min:1|max:12',
        ]);

        $device = Device::findOrFail($request->id_device);
        $durasi = (int) $request->durasi;
        $totalHarga = $device->harga_per_jam * $durasi;

        $booking = Booking::create([
            'kode_booking' => 'GH-' . now()->format('dmy') . '-' . rand(100, 999),
            'id_user' => Auth::id(), // Dihubungkan ke user staff pembuat
            'id_device' => $device->id,
            'waktu_mulai' => now(),
            'waktu_selesai' => now()->addHours($durasi),
            'durasi' => $durasi,
            'total_harga' => $totalHarga,
            'status' => 'bermain',
            'status_konfirmasi' => 'dikonfirmasi',
            'id_staff' => Auth::id(),
            'jenis_booking' => 'langsung',
        ]);

        $device->update(['status' => 'bermain']);

        return back()->with('success', "Booking walk-in {$booking->kode_booking} berhasil dibuat dan dimulai!");
    }

    // Laporkan Kendala / Ubah Status Device ke Perbaikan
    public function storeMaintenance(Request $request)
    {
        $request->validate([
            'id_device' => 'required|exists:devices,id',
        ]);

        $device = Device::findOrFail($request->id_device);
        $device->update(['status' => 'perbaikan']);

        return back()->with('success', "Perangkat {$device->nama_device} dinonaktifkan ke status perbaikan.");
    }
}