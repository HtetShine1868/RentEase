<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleApplicationController; // ADD THIS LINE
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Email verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('throttle:6,1')->name('verification.notice');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    // Dashboard (protected by auth and verified email)
    Route::get('/dashboard', [DashboardController::class, 'index'])
                ->middleware('verified')
                ->name('dashboard');

    // Role Application Routes - MOVED HERE, inside auth middleware
    Route::middleware('verified')->group(function () {
        Route::prefix('role/apply')->name('role.apply.')->group(function () {
            Route::get('/', [RoleApplicationController::class, 'index'])->name('index');
            Route::get('/{roleType}', [RoleApplicationController::class, 'create'])->name('create');
            Route::post('/{roleType}', [RoleApplicationController::class, 'store'])->name('store');
            Route::get('/show/{id}', [RoleApplicationController::class, 'show'])->name('show');
            Route::delete('/{id}', [RoleApplicationController::class, 'destroy'])->name('destroy');
        });
    });

    // Protected routes with role-based access
    Route::middleware(['role:SUPERADMIN'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    Route::middleware(['role:OWNER'])->group(function () {
        Route::get('/owner', function () {
            return view('owner.dashboard');
        })->name('owner.dashboard');
    });

    Route::middleware(['role:FOOD'])->group(function () {
        Route::get('/food-provider', function () {
            return view('food.dashboard');
        })->name('food.dashboard');
    });

    Route::middleware(['role:LAUNDRY'])->group(function () {
        Route::get('/laundry-provider', function () {
            return view('laundry.dashboard');
        })->name('laundry.dashboard');
    });
});