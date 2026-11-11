<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Membership;

class CustomerDashboardController extends Controller
{
    // ==========================================
    // BERANDA CUSTOMER - DEVICES
    // ==========================================
    public function index()
    {
        $dummyDevices = [
            [
                'nama_device' => 'PlayStation 5 - A',
                'tipe' => 'PS5',
                'kelas' => 'Reguler',
                'spesifikasi' => 'TV 4K 50 inch • 2 Controller DualSense • Game Terbaru',
                'harga_per_jam' => 25000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 5 - B',
                'tipe' => 'PS5',
                'kelas' => 'Reguler',
                'spesifikasi' => 'TV 4K 50 inch • 2 Controller DualSense • Game Terbaru',
                'harga_per_jam' => 25000,
                'status' => 'penuh',
            ],
            [
                'nama_device' => 'PC Gaming - VIP',
                'tipe' => 'PC',
                'kelas' => 'VIP',
                'spesifikasi' => 'Intel Core i7 Gen 13 • 32GB RAM • RTX 4070 • 240Hz',
                'harga_per_jam' => 20000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 4 - VIP',
                'tipe' => 'PS4',
                'kelas' => 'VIP',
                'spesifikasi' => 'TV FHD 43 inch • 2 Controller DS4 • Sofa Recliner',
                'harga_per_jam' => 15000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PC Gaming',
                'tipe' => 'PC',
                'kelas' => 'Reguler',
                'spesifikasi' => 'Intel Core i5 Gen 12 • 16GB RAM • RTX 3060 • 165Hz',
                'harga_per_jam' => 12000,
                'status' => 'penuh',
            ],
            [
                'nama_device' => 'PlayStation 4 - A',
                'tipe' => 'PS4',
                'kelas' => 'Reguler',
                'spesifikasi' => 'TV FHD 40 inch • 2 Controller DS4 • Game Lengkap',
                'harga_per_jam' => 10000,
                'status' => 'maintenance',
            ],
            [
                'nama_device' => 'PlayStation 4 - B',
                'tipe' => 'PS4',
                'kelas' => 'Reguler',
                'spesifikasi' => 'TV FHD 40 inch • 2 Controller DS4 • Game Lengkap',
                'harga_per_jam' => 10000,
                'status' => 'tersedia',
            ],
            [
                'nama_device' => 'PlayStation 5 - VIP',
                'tipe' => 'PS5',
                'kelas' => 'VIP',
                'spesifikasi' => 'TV OLED 4K 55 inch • 2 DualSense Edge • Private Room',
                'harga_per_jam' => 30000,
                'status' => 'maintenance',
            ],
        ];

        // Urutkan Reguler terlebih dahulu, kemudian VIP
        $urutanKelas = [
            'Reguler' => 1,
            'VIP' => 2,
        ];

        usort($dummyDevices, function ($a, $b) use ($urutanKelas) {
            return ($urutanKelas[$a['kelas']] ?? 99)
                <=> ($urutanKelas[$b['kelas']] ?? 99);
        });

        return view('customer.dashboard', compact('dummyDevices'));
    }

    // ==========================================
    // HALAMAN MEMBERSHIP
    // ==========================================
    public function membership()
    {
        $user = Auth::user();

        $membership = Membership::where('id_user', $user->id)->first();

        return view('customer.membership', compact('membership'));
    }

    // ==========================================
    // AKTIVASI MEMBERSHIP PERMANEN
    // ==========================================
    public function aktifkanMembership()
    {
        $user = Auth::user();

        $membership = Membership::where('id_user', $user->id)->first();

        if ($membership && $membership->status === 'aktif') {
            return back()->with(
                'error',
                'Kamu sudah menjadi member SI-GAMER.'
            );
        }

        // Membership berlaku permanen tanpa tanggal berakhir
        if (!$membership) {
            $membership = new Membership();
        }

        $membership->forceFill([
            'id_user' => $user->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_berakhir' => null,
            'status' => 'aktif',
        ])->save();

        return redirect()
            ->route('customer.membership')
            ->with('success', 'Selamat! Pendaftaran membership berhasil.');
    }

    // ==========================================
    // HALAMAN PROFILE
    // ==========================================
    public function profile()
    {
        $user = Auth::user();

        return view('customer.profile', compact('user'));
    }

    // ==========================================
    // UPDATE PROFILE
    // ==========================================
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id',

            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
        ];

        $fotoLama = $user->foto_profil;

        // Upload foto profil baru
        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')
                ->store('foto-profil', 'public');
        }

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Simpan perubahan tanpa bergantung pada $fillable
        $user->forceFill($data)->save();

        // Hapus foto lama setelah foto baru berhasil disimpan
        if (
            $request->hasFile('foto_profil') &&
            $fotoLama
        ) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
