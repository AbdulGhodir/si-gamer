<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm() {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else if (Auth::user()->role === 'staff') {
                return redirect()->intended('/staff/dashboard');
            } else {
                return redirect()->intended('/customer/dashboard');
            }
        }

        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ],  [
            'email.required'    => 'Email wajib diisi!',
            'email.email'       => 'Format email tidak valid!',
            'password.required' => 'Password wajib diisi!',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->remember)){
            $request->session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else if ($role === 'staff') {
                return redirect()->intended('/staff/dashboard');
            } else {
                return redirect()->intended('/customer/dashboard');
            }
        } else {
            return back()->withErrors([
                'email' => 'Email atau Password salah'
            ])->onlyInput('email');
        }
    }

    public function registerForm() {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else if (Auth::user()->role === 'staff') {
                return redirect()->intended('/staff/dashboard');
            } else {
                return redirect()->intended('/customer/dashboard');
            }
        }

        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi!',
            'nama.max'           => 'Nama tidak boleh lebih dari 50 karakter.',
            'email.required'     => 'Email tidak boleh kosong!',
            'email.email'        => 'Format email tidak valid!',
            'email.unique'       => 'Email sudah terdaftar!',
            'password.required'  => 'Password wajib diisi!',
            'password.min'       => 'Password minimal harus 8 karakter!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/customer/dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
}