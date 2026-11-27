<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerProfileController extends Controller
{
    public function membership()
    {
        $user = Auth::user();

        $membership = Membership::where('id_user', $user->id)->first();

        return view('customer.membership', compact('membership'));
    }

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

    public function profile()
    {
        $user = Auth::user();

        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id',

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

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->forceFill($data)->save();

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
