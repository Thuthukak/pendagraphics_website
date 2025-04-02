<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    // Guest Middleware (only for login & register pages)
    Route::middleware('guest')->group(function () {
        Route::get('/auth', function () {
            return view('admin.auth');
        })->name('admin.auth');
    });
    
    // Authenticated Middleware (for authenticated admin actions)
    Route::middleware('auth')->group(function () {
        Route::get('/{any}', function () { return view('admin.dashboard'); })->where('any', '.*');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/bookings', [BookingController::class, 'adminBookings'])->name('admin.bookings');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Client Routes (Public Routes - Non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/bookings', function () {
        return view('bookings');
    });

    Route::post('/book', [BookingController::class, 'create'])->name('book');
});

// API Routes (Public API)
Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/data', [BookingController::class, 'adminBookingsData'])->name('admin.bookings.data');
    
});

require __DIR__.'/auth.php';
