<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Staff\StaffController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'loginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'active.staff'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [AdminDashboardController::class, 'destroyUser'])->name('admin.users.destroy');

        Route::get('/admin/devices', [AdminDashboardController::class, 'devices'])->name('admin.devices');
        Route::post('/admin/devices', [AdminDashboardController::class, 'storeDevice'])->name('admin.devices.store');
        Route::put('/admin/devices/{id}', [AdminDashboardController::class, 'updateDevice'])->name('admin.devices.update');
        Route::delete('/admin/devices/{id}', [AdminDashboardController::class, 'destroyDevice'])->name('admin.devices.destroy');

        Route::get('/admin/bookings', [AdminDashboardController::class, 'bookings'])->name('admin.bookings');

        Route::get('/admin/maintenance', [AdminDashboardController::class, 'maintenance'])->name('admin.maintenance');
        Route::patch('/admin/maintenance/{id}/toggle', [AdminDashboardController::class, 'toggleMaintenanceStatus'])->name('admin.maintenance.toggle');

        Route::get('/admin/reports', [AdminDashboardController::class, 'reports'])->name('admin.reports');
    });

    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/staff/bookings', [StaffController::class, 'bookings'])->name('staff.bookings');
        Route::get('/staff/sessions', [StaffController::class, 'sessions'])->name('staff.sessions');
        Route::get('/staff/booking/walk-in', [StaffController::class, 'walkIn'])->name('staff.booking.walkin');
        Route::get('/staff/devices', [StaffController::class, 'devices'])->name('staff.devices');
        Route::get('/staff/maintenance', [StaffController::class, 'maintenance'])->name('staff.maintenance');
        Route::post('/staff/booking/{id}/check-in', [StaffController::class, 'checkIn'])->name('staff.booking.checkin');
        Route::post('/staff/booking/{id}/check-out', [StaffController::class, 'checkOut'])->name('staff.booking.checkout');
        Route::post('/staff/booking/{id}/add-duration', [StaffController::class, 'addDuration'])->name('staff.booking.add_duration');
        Route::post('/staff/booking/{id}/confirm-payment', [StaffController::class, 'confirmPayment'])->name('staff.booking.confirm_payment');
        Route::post('/staff/bookings', [StaffController::class, 'storeBooking'])->name('staff.booking.store');
        Route::post('/staff/booking/walk-in', [StaffController::class, 'storeWalkIn'])->name('staff.booking.walkin.store');
        Route::post('/staff/maintenance', [StaffController::class, 'storeMaintenance'])->name('staff.maintenance.store');
        Route::post('/staff/maintenance/{id}/complete', [StaffController::class, 'completeMaintenance'])->name('staff.maintenance.complete');
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

        Route::get('/customer/booking', [CustomerBookingController::class, 'booking'])->name('customer.booking');
        Route::post('/customer/booking', [CustomerBookingController::class, 'store'])->name('customer.booking.store');
        Route::get('/customer/riwayat', [CustomerBookingController::class, 'riwayat'])->name('customer.riwayat');

        Route::get('/customer/payment/{payment}/checkout', [CustomerPaymentController::class, 'checkout'])->name('customer.payment.checkout');

        Route::get('/customer/profile', [CustomerProfileController::class, 'profile'])->name('customer.profile');
        Route::put('/customer/profile', [CustomerProfileController::class, 'updateProfile'])->name('customer.profile.update');

        Route::get('/customer/membership', [CustomerProfileController::class, 'membership'])->name('customer.membership');
        Route::post('/customer/membership/aktifkan', [CustomerProfileController::class, 'aktifkanMembership'])->name('customer.membership.aktifkan');
    });
});

Route::post('/midtrans/notification', [CustomerPaymentController::class, 'handle'])->name('midtrans.notification');