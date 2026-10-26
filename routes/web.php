<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/staff/dashboard', function () {
        return view('staff.dashboard', ['user' => Auth::user()]);
    })->name('staff.dashboard');

    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])
        ->name('customer.dashboard');

    Route::post('/customer/booking', [CustomerDashboardController::class, 'storeBooking'])
        ->name('customer.booking.store');

    Route::get('/admin/users', [AdminDashboardController::class, 'users'])
        ->name('admin.users');

    Route::get('/admin/devices', [AdminDashboardController::class, 'devices'])
        ->name('admin.devices');

    Route::get('/admin/tariffs', [AdminDashboardController::class, 'tariffs'])
        ->name('admin.tariffs');

    Route::get('/admin/promos', [AdminDashboardController::class, 'promos'])
        ->name('admin.promos');

    Route::get('/admin/transactions', [AdminDashboardController::class, 'transactions'])
        ->name('admin.transactions');

    Route::get('/admin/bookings', [AdminDashboardController::class, 'bookings'])
        ->name('admin.bookings');

    Route::get('/admin/maintenance', [AdminDashboardController::class, 'maintenance'])
        ->name('admin.maintenance');

    Route::get('/admin/reports', [AdminDashboardController::class, 'reports'])
        ->name('admin.reports');

    Route::post('/customer/membership/aktifkan', [
        CustomerDashboardController::class,
        'aktifkanMembership'
    ])->name('customer.membership.aktifkan');
});