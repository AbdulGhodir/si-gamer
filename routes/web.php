<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Staff\StaffController;

Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/admin/devices', [AdminDashboardController::class, 'devices'])->name('admin.devices');
    Route::get('/admin/tariffs', [AdminDashboardController::class, 'tariffs'])->name('admin.tariffs');
    Route::get('/admin/promos', [AdminDashboardController::class, 'promos'])->name('admin.promos');
    Route::get('/admin/transactions', [AdminDashboardController::class, 'transactions'])->name('admin.transactions');
    Route::get('/admin/bookings', [AdminDashboardController::class, 'bookings'])->name('admin.bookings');
    Route::get('/admin/maintenance', [AdminDashboardController::class, 'maintenance'])->name('admin.maintenance');
    Route::get('/admin/reports', [AdminDashboardController::class, 'reports'])->name('admin.reports');

    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::post('/staff/booking/{id}/check-in', [StaffController::class, 'checkIn'])->name('staff.booking.checkin');
    Route::post('/staff/booking/{id}/check-out', [StaffController::class, 'checkOut'])->name('staff.booking.checkout');
    Route::post('/staff/booking/{id}/add-duration', [StaffController::class, 'addDuration'])->name('staff.booking.add_duration');
    Route::post('/staff/booking/walk-in', [StaffController::class, 'storeWalkIn'])->name('staff.booking.walkin.store');
    Route::post('/staff/maintenance', [StaffController::class, 'storeMaintenance'])->name('staff.maintenance.store');

    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/booking', [CustomerBookingController::class, 'booking'])->name('customer.booking');
    Route::post('/customer/booking', [CustomerDashboardController::class, 'storeBooking'])->name('customer.booking.store');
    Route::get('/customer/riwayat', [CustomerBookingController::class, 'riwayat'])->name('customer.riwayat');
    Route::post('/customer/membership/aktifkan', [CustomerDashboardController::class, 'aktifkanMembership'])->name('customer.membership.aktifkan');
});
