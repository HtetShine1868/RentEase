<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (from Breeze)
require __DIR__.'/auth.php';

// Email verification notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard redirector
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-specific dashboards
    Route::middleware(['role:USER'])->group(function () {
        Route::get('/user/dashboard', function () {
            return view('dashboard.user');
        })->name('user.dashboard');
    });

    Route::middleware(['role:SUPERADMIN'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboard.admin');
        })->name('admin.dashboard');
    });

    Route::middleware(['role:OWNER'])->group(function () {
        Route::get('/owner/dashboard', function () {
            return view('dashboard.owner');
        })->name('owner.dashboard');
    });

    Route::middleware(['role:FOOD'])->group(function () {
        Route::get('/food/dashboard', function () {
            return view('dashboard.food');
        })->name('food.dashboard');
    });

    Route::middleware(['role:LAUNDRY'])->group(function () {
        Route::get('/laundry/dashboard', function () {
            return view('dashboard.laundry');
        })->name('laundry.dashboard');
    });

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role application routes (will create these later)
    Route::prefix('role-application')->name('role-application.')->group(function () {
        Route::get('/create', function () {
            return view('role-application.create');
        })->name('create');
        
        Route::post('/', function () {
            // Will handle application submission
        })->name('store');
        
        Route::get('/status', function () {
            return view('role-application.status');
        })->name('status');
    });
});