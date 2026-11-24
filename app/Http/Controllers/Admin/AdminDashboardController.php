<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Device;
use App\Models\Maintenance;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    private function isPaidBooking(Booking $booking): bool
    {
        $statusRaw = strtolower((string) ($booking->payment->status_pembayaran ?? ''));
        return in_array($statusRaw, ['lunas', 'paid', 'settlement', 'capture', 'berhasil'], true);
    }

    public function index()
    {
        $user = Auth::user();

        $bookings = Booking::with(['device', 'payment'])->get();
        $devices = Device::all();

        $paidBookings = $bookings->filter(fn (Booking $b) => $this->isPaidBooking($b) && $b->status !== 'dibatalkan');

        $totalPendapatan = $paidBookings->sum(function (Booking $b) {
            $paymentAmount = (int) ($b->payment->jumlah ?? 0);
            return $paymentAmount > 0 ? $paymentAmount : (int) ($b->total_harga ?? $b->subtotal ?? 0);
        });

        $todayBookings = $bookings->filter(function (Booking $b) {
            return $b->waktu_mulai && Carbon::parse($b->waktu_mulai)->isToday() && $b->status !== 'dibatalkan';
        });

        $todayPaidCount = $todayBookings->filter(fn (Booking $b) => $this->isPaidBooking($b))->count();
        $todayUnpaidCount = max(0, $todayBookings->count() - $todayPaidCount);

        $todayRevenue = $todayBookings
            ->filter(fn (Booking $b) => $this->isPaidBooking($b))
            ->sum(function (Booking $b) {
                $paymentAmount = (int) ($b->payment->jumlah ?? 0);
                return $paymentAmount > 0 ? $paymentAmount : (int) ($b->total_harga ?? $b->subtotal ?? 0);
            });

        $totalDevices = max(1, $devices->count());
        $activeDevicesCount = $devices->whereIn('status', ['bermain', 'dipesan'])->count();
        $utilitasPersen = round(($activeDevicesCount / $totalDevices) * 100);

        $stats = [
            'pendapatan'          => 'Rp' . number_format((int) $totalPendapatan, 0, ',', '.'),
            'transaksi_lunas'     => $paidBookings->count(),
            'booking_hari_ini'    => $todayBookings->count(),
            'booking_hari_ini_sub'=> $todayPaidCount . ' lunas · ' . $todayUnpaidCount . ' belum',
            'utilitas'            => $utilitasPersen . '%',
            'sesi_aktif_sub'      => $activeDevicesCount . ' sesi aktif',
            'operasional_harian'  => 'Rp' . number_format((int) $todayRevenue, 0, ',', '.'),
            'operasional_sub'     => $todayPaidCount . ' booking lunas',
        ];

        $activeMaintenance = Maintenance::with('device')
            ->where('status', 'berlangsung')
            ->latest('id')
            ->first();

        return view('admin.dashboard', compact('stats', 'user', 'activeMaintenance'));
    }

    public function users()
    {
        $user = Auth::user();

        $users = User::where('role', 'staff')
            ->orderBy('id')
            ->get()
            ->map(function (User $u) {
                return [
                    'id'     => $u->id,
                    'nama'   => $u->nama,
                    'email'  => $u->email,
                    'role'   => 'Staff',
                    'status' => 'Aktif',
                ];
            });

        return view('admin.users', compact('users', 'user'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:255',
        ]);

        User::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff',
        ]);

        return redirect()->route('admin.users')->with('success', 'Akun staff berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $staff = User::where('role', 'staff')->findOrFail($id);

        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:6|max:255',
        ]);

        $dataToUpdate = [
            'nama'  => $validated['nama'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $staff->update($dataToUpdate);

        return redirect()->route('admin.users')->with('success', 'Data akun staff berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $staff = User::where('role', 'staff')->findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.users')->with('success', 'Akun staff berhasil dihapus.');
    }

    public function devices()
    {
        $user = Auth::user();
        $devices = Device::orderBy('id')->get();

        return view('admin.devices', compact('devices', 'user'));
    }

    public function storeDevice(Request $request)
    {
        $validated = $request->validate([
            'nama_device'   => 'required|string|max:255',
            'tipe'          => 'required|in:PS4,PS5,PC',
            'harga_per_jam' => 'required|integer|min:0',
            'status'        => 'required|in:tersedia,dipesan,bermain,perbaikan,offline',
            'kategori'      => 'nullable|string|max:100',
            'spesifikasi'   => 'nullable|string|max:500',
        ]);

        Device::create($validated);

        return redirect()->route('admin.devices')->with('success', 'Unit perangkat berhasil ditambahkan.');
    }

    public function updateDevice(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'nama_device'   => 'required|string|max:255',
            'tipe'          => 'required|in:PS4,PS5,PC',
            'harga_per_jam' => 'required|integer|min:0',
            'status'        => 'required|in:tersedia,dipesan,bermain,perbaikan,offline',
            'kategori'      => 'nullable|string|max:100',
            'spesifikasi'   => 'nullable|string|max:500',
        ]);

        $device->update($validated);

        return redirect()->route('admin.devices')->with('success', 'Data unit perangkat berhasil diperbarui.');
    }

    public function destroyDevice($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('admin.devices')->with('success', 'Unit perangkat berhasil dihapus.');
    }

    public function bookings()
    {
        $user = Auth::user();

        $bookingRecords = Booking::with(['user', 'device', 'payment'])
            ->orderByDesc('id')
            ->get();

        $bookings = $bookingRecords->map(function (Booking $b) {
            $isConfirmed = ($b->status_konfirmasi === 'dikonfirmasi') || in_array($b->status, ['bermain', 'selesai'], true);
            $isPaid = $this->isPaidBooking($b);

            $mulaiFormatted = $b->waktu_mulai
                ? Carbon::parse($b->waktu_mulai)->format('d/m/Y H:i')
                : '-';

            $unitLabel = $b->device?->nama_device
                ? ' (' . $b->device->nama_device . ')'
                : '';

            $nominal = (int) ($b->total_harga ?? $b->subtotal ?? 0);

            return [
                'model_id'     => $b->id,
                'id'           => $b->kode_booking,
                'pelanggan'    => $b->user?->nama ?? 'Pelanggan',
                'jadwal'       => $mulaiFormatted . $unitLabel,
                'durasi'       => ($b->durasi ?? 1) . ' Jam',
                'total'        => 'Rp' . number_format($nominal, 0, ',', '.'),
                'status_bayar' => $isPaid ? 'Lunas' : 'Belum bayar',
                'status'       => $isConfirmed ? 'Dikonfirmasi' : 'Menunggu',
            ];
        });

        $customers = User::where('role', 'customer')->orderBy('nama')->get();
        $devices = Device::orderBy('nama_device')->get();

        return view('admin.bookings', compact('bookings', 'customers', 'devices', 'user'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'kode_booking' => 'nullable|string|max:50|unique:bookings,kode_booking',
            'id_user'      => 'required|exists:users,id',
            'id_device'    => 'required|exists:devices,id',
            'waktu_mulai'  => 'required|date',
            'durasi'       => 'required|integer|min:1|max:24',
            'total_harga'  => 'required|integer|min:0',
            'status_bayar' => 'required|in:Lunas,Belum bayar',
            'status'       => 'required|in:Dikonfirmasi,Menunggu',
        ]);

        $device = Device::findOrFail($validated['id_device']);
        $mulai = Carbon::parse($validated['waktu_mulai']);
        $durasi = (int) $validated['durasi'];
        $selesai = (clone $mulai)->addHours($durasi);

        $totalHarga = (int) $validated['total_harga'];
        if ($totalHarga <= 0 && isset($device->harga_per_jam)) {
            $totalHarga = (int) $device->harga_per_jam * $durasi;
        }

        $kodeBooking = !empty($validated['kode_booking'])
            ? strtoupper(trim($validated['kode_booking']))
            : 'BOK-' . strtoupper(Str::random(6));

        $isConfirmed = $validated['status'] === 'Dikonfirmasi';
        $isPaid = $validated['status_bayar'] === 'Lunas';

        DB::transaction(function () use (
            $kodeBooking,
            $validated,
            $mulai,
            $selesai,
            $durasi,
            $totalHarga,
            $isConfirmed,
            $isPaid
        ) {
            $booking = Booking::create([
                'kode_booking'      => $kodeBooking,
                'id_user'           => $validated['id_user'],
                'id_device'         => $validated['id_device'],
                'id_staff'          => Auth::id(),
                'waktu_mulai'       => $mulai,
                'waktu_selesai'     => $selesai,
                'durasi'            => $durasi,
                'subtotal'          => $totalHarga,
                'diskon'            => 0,
                'total_harga'       => $totalHarga,
                'status'            => $isConfirmed ? 'bermain' : 'menunggu',
                'status_konfirmasi' => $isConfirmed ? 'dikonfirmasi' : 'belum_dikonfirmasi',
                'jenis_booking'     => 'langsung',
            ]);

            Payment::create([
                'id_booking'        => $booking->id,
                'kode_transaksi'    => 'TRX-' . strtoupper(Str::random(8)),
                'metode_pembayaran' => 'tunai',
                'jumlah'            => $totalHarga,
                'status_pembayaran' => $isPaid ? 'lunas' : 'belum_bayar',
                'waktu_pembayaran'  => $isPaid ? now() : null,
            ]);
        });

        return redirect()->route('admin.bookings')->with('success', 'Booking berhasil disimpan ke database.');
    }

    public function toggleBookingStatus($id)
    {
        $booking = Booking::findOrFail($id);

        $currentlyConfirmed = ($booking->status_konfirmasi === 'dikonfirmasi') || in_array($booking->status, ['bermain', 'selesai'], true);

        if ($currentlyConfirmed) {
            $booking->update([
                'status_konfirmasi' => 'belum_dikonfirmasi',
                'status'            => 'menunggu',
            ]);
        } else {
            $booking->update([
                'status_konfirmasi' => 'dikonfirmasi',
                'status'            => 'bermain',
            ]);
        }

        return redirect()->route('admin.bookings')->with('success', 'Status booking berhasil diperbarui.');
    }

    public function togglePaymentStatus($id)
    {
        $booking = Booking::with('payment')->findOrFail($id);
        $nominal = (int) ($booking->total_harga ?? $booking->subtotal ?? 0);

        if (!$booking->payment) {
            Payment::create([
                'id_booking'        => $booking->id,
                'kode_transaksi'    => 'TRX-' . strtoupper(Str::random(8)),
                'metode_pembayaran' => 'tunai',
                'jumlah'            => $nominal,
                'status_pembayaran' => 'lunas',
                'waktu_pembayaran'  => now(),
            ]);
        } else {
            $isPaid = $this->isPaidBooking($booking);

            $booking->payment->update([
                'status_pembayaran' => $isPaid ? 'belum_bayar' : 'lunas',
                'jumlah'            => $booking->payment->jumlah ?: $nominal,
                'waktu_pembayaran'  => $isPaid ? null : now(),
            ]);
        }

        return redirect()->route('admin.bookings')->with('success', 'Status pembayaran booking berhasil diperbarui.');
    }

    public function maintenance()
    {
        $user = Auth::user();

        $records = Maintenance::with('device')
            ->orderByDesc('id')
            ->get();

        $maintenances = $records->map(function (Maintenance $m) {
            $isSelesai = ($m->status === 'selesai');

            return [
                'model_id' => $m->id,
                'ticket'   => 'MT-' . str_pad((string) $m->id, 3, '0', STR_PAD_LEFT),
                'unit'     => $m->device?->nama_device ?? ('Device #' . $m->id_device),
                'masalah'  => $m->keterangan ?? '-',
                'waktu'    => $m->waktu_mulai ? Carbon::parse($m->waktu_mulai)->format('d/m/Y H:i') : '-',
                'status'   => $isSelesai ? 'Selesai' : 'Dalam Penanganan',
            ];
        });

        $devices = Device::orderBy('nama_device')->get();

        return view('admin.maintenance', compact('maintenances', 'devices', 'user'));
    }

    public function storeMaintenance(Request $request)
    {
        $validated = $request->validate([
            'id_device'   => 'required|exists:devices,id',
            'keterangan'  => 'required|string|max:1000',
            'waktu_mulai' => 'nullable|date',
            'status'      => 'required|in:Dalam Penanganan,Selesai',
        ]);

        $isSelesai = $validated['status'] === 'Selesai';
        $mulai = !empty($validated['waktu_mulai']) ? Carbon::parse($validated['waktu_mulai']) : now();

        DB::transaction(function () use ($validated, $isSelesai, $mulai) {
            Maintenance::create([
                'id_device'     => $validated['id_device'],
                'keterangan'    => $validated['keterangan'],
                'waktu_mulai'   => $mulai,
                'waktu_selesai' => $isSelesai ? now() : null,
                'status'        => $isSelesai ? 'selesai' : 'berlangsung',
            ]);

            $device = Device::find($validated['id_device']);
            if ($device) {
                $device->update([
                    'status' => $isSelesai ? 'tersedia' : 'perbaikan',
                ]);
            }
        });

        return redirect()->route('admin.maintenance')->with('success', 'Tiket maintenance berhasil disimpan ke database.');
    }

    public function toggleMaintenanceStatus($id)
    {
        $maintenance = Maintenance::with('device')->findOrFail($id);
        $isSelesai = ($maintenance->status === 'selesai');

        DB::transaction(function () use ($maintenance, $isSelesai) {
            if ($isSelesai) {
                $maintenance->update([
                    'status'        => 'berlangsung',
                    'waktu_selesai' => null,
                ]);
                if ($maintenance->device) {
                    $maintenance->device->update(['status' => 'perbaikan']);
                }
            } else {
                $maintenance->update([
                    'status'        => 'selesai',
                    'waktu_selesai' => now(),
                ]);
                if ($maintenance->device) {
                    $maintenance->device->update(['status' => 'tersedia']);
                }
            }
        });

        return redirect()->route('admin.maintenance')->with('success', 'Status tiket maintenance berhasil diperbarui.');
    }

    public function reports()
    {
        $user = Auth::user();

        $devices = Device::all();
        $paidBookings = Booking::with(['device', 'payment'])
            ->where('status', '!=', 'dibatalkan')
            ->get()
            ->filter(fn (Booking $b) => $this->isPaidBooking($b));

        $totalPendapatan = $paidBookings->sum(function (Booking $b) {
            $paymentAmount = (int) ($b->payment->jumlah ?? 0);
            return $paymentAmount > 0 ? $paymentAmount : (int) ($b->total_harga ?? $b->subtotal ?? 0);
        });

        $totalSesiLunas = $paidBookings->count();
        $todayRevenue = $paidBookings
            ->filter(fn (Booking $b) => $b->waktu_mulai && Carbon::parse($b->waktu_mulai)->isToday())
            ->sum(function (Booking $b) {
                $paymentAmount = (int) ($b->payment->jumlah ?? 0);
                return $paymentAmount > 0 ? $paymentAmount : (int) ($b->total_harga ?? $b->subtotal ?? 0);
            });

        $categories = [
            'PS4' => 'PlayStation 4',
            'PS5' => 'PlayStation 5',
            'PC'  => 'PC Gaming',
        ];

        $breakdown = collect($categories)->map(function (string $label, string $tipeCode) use ($devices, $paidBookings) {
            $typeDevices = $devices->where('tipe', $tipeCode);
            $typeDeviceIds = $typeDevices->pluck('id')->all();

            $typeBookings = $paidBookings->filter(function (Booking $b) use ($typeDeviceIds, $tipeCode) {
                if (in_array($b->id_device, $typeDeviceIds, true)) {
                    return true;
                }
                return strtoupper((string) ($b->device->tipe ?? '')) === $tipeCode;
            });

            $sesiCount = $typeBookings->count();
            $revenue = $typeBookings->sum(function (Booking $b) {
                $paymentAmount = (int) ($b->payment->jumlah ?? 0);
                return $paymentAmount > 0 ? $paymentAmount : (int) ($b->total_harga ?? $b->subtotal ?? 0);
            });

            $unitCount = max(1, $typeDevices->count());
            $activeCount = $typeDevices->whereIn('status', ['bermain', 'dipesan'])->count();
            $utilisasi = $devices->where('tipe', $tipeCode)->isEmpty()
                ? 0
                : round(($activeCount / $unitCount) * 100);

            return [
                'kategori'   => $label,
                'sesi'       => $sesiCount . ' Sesi',
                'utilisasi'  => $utilisasi . '%',
                'pendapatan' => 'Rp' . number_format((int) $revenue, 0, ',', '.'),
                'status'     => $utilisasi >= 50 ? 'Optimal' : 'Stabil',
            ];
        })->values();

        $totalDevices = max(1, $devices->count());
        $activeDevicesCount = $devices->whereIn('status', ['bermain', 'dipesan'])->count();
        $overallUtilisasi = round(($activeDevicesCount / $totalDevices) * 100);

        $reportSummary = [
            'pendapatan'         => 'Rp' . number_format((int) $totalPendapatan, 0, ',', '.'),
            'sesi_lunas'         => $totalSesiLunas . ' Sesi Lunas',
            'operasional_harian' => 'Rp' . number_format((int) $todayRevenue, 0, ',', '.'),
            'utilisasi'          => $overallUtilisasi . '%',
            'sesi_aktif'         => $activeDevicesCount . ' sesi aktif',
        ];

        return view('admin.reports', compact('user', 'breakdown', 'reportSummary'));
    }
}