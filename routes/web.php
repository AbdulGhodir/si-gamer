<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('auth.login');
// });

Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard', ['user' => Auth::user()]);
})->middleware('auth');

Route::get('/staff/dashboard', function () {
    return view('staff.dashboard', ['user' => Auth::user()]);
})->middleware('auth');

Route::get('/customer/dashboard', function () {
    return view('customer.dashboard', ['user' => Auth::user()]);
})->middleware('auth');