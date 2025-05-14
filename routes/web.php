<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\HomeController;

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

    Route::prefix('services')->group(function () {
        Route::get('/web-design', [ServicesController::class, 'indexWebDesign'])->name('web-design.index');
        Route::get('/graphic-design', [ServicesController::class, 'indexGraphicDesign'])->name('graphic-design.index');
        Route::get('/product-design', [ServicesController::class, 'indexProductDesign'])->name('product-design.index');
        Route::get('/identity-design', [ServicesController::class, 'indexIdentityDesign'])->name('identity-design.index');
        Route::get('/e-commerce', [ServicesController::class, 'indexECommerce'])->name('e-commerce.index');
        Route::get('/digital-marketing', [ServicesController::class, 'indexDigitalMarketing'])->name('digital-marketing.index');
        
    });

    Route::post('/contact-form', [ContactController::class, 'contactForm'])->name('contact.form');
    Route::get('/faq', [HomeController::class, 'FaqIndex'])->name('faq.index');
    Route::get('/about-us', [HomeController::class, 'AboutUsIndex'])->name('about-us.index');
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
    Route::get('/contact-us', [HomeController::class, 'HomeIndex'])->name('home.index');
   
});

// API Routes (Public API)
Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/data', [BookingController::class, 'adminBookingsData'])->name('admin.bookings.data');
    Route::post('/bookings', [BookingController::class, 'store']);
    
});

require __DIR__.'/auth.php';
