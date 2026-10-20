<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;

Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard', ['user' => Auth::user()]);
    })->name('admin.dashboard');

    Route::get('/staff/dashboard', function () {
        return view('staff.dashboard', ['user' => Auth::user()]);
    })->name('staff.dashboard');

    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])
        ->name('customer.dashboard');

    Route::post('/customer/booking', [CustomerDashboardController::class, 'storeBooking'])
        ->name('customer.booking.store');

    Route::post('/customer/membership/aktifkan', [
        CustomerDashboardController::class,
        'aktifkanMembership'
    ])->name('customer.membership.aktifkan');
});