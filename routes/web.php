<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
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

    // ============ USER DASHBOARD ROUTES ============
    Route::middleware(['verified'])->group(function () {
        // Rental Management
        Route::prefix('rental')->name('rental.')->group(function () {
            Route::get('/', function () {
                return view('rental.index');
            })->name('index');
            
            Route::get('/search', function () {
                return view('rental.search');
            })->name('search');
        });
        
        // Food Services
        Route::prefix('food')->name('food.')->group(function () {
            Route::get('/', function () {
                return view('food.index');
            })->name('index');
        });
        
        // Laundry Services
        Route::prefix('laundry')->name('laundry.')->group(function () {
            Route::get('/', function () {
                return view('laundry.index');
            })->name('index');
        });
        
        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', function () {
                return view('payments.index');
            })->name('index');
        });
        
        // Profile
        Route::get('/profile', function () {
            return view('profile.edit');
        })->name('profile.edit');
        
        // Role Application Routes
        Route::prefix('role/apply')->name('role.apply.')->group(function () {
            Route::get('/', [RoleApplicationController::class, 'index'])->name('index');
            Route::get('/{roleType}', [RoleApplicationController::class, 'create'])->name('create');
            Route::post('/{roleType}', [RoleApplicationController::class, 'store'])->name('store');
            Route::get('/show/{id}', [RoleApplicationController::class, 'show'])->name('show');
            Route::delete('/{id}', [RoleApplicationController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ ROLE-SPECIFIC ROUTES ============
    
    // SuperAdmin Routes
    Route::middleware(['role:SUPERADMIN'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboard.admin', ['title' => 'SuperAdmin Dashboard']);
        })->name('admin.dashboard');
    });

    // Owner Routes
    Route::middleware(['role:OWNER'])->group(function () {
        Route::resource('properties', PropertyController::class);
        
        // Property status update
        Route::post('/properties/{property}/status', [PropertyController::class, 'updateStatus'])->name('properties.status');
        
        // Property analytics
        Route::get('/properties/{property}/analytics', [PropertyController::class, 'analytics'])->name('properties.analytics');
        
        // Room management for hostels
        Route::prefix('properties/{property}/rooms')->name('rooms.')->group(function () {
            Route::get('/create', [RoomController::class, 'create'])->name('create');
            Route::post('/', [RoomController::class, 'store'])->name('store');
            Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
            Route::put('/{room}', [RoomController::class, 'update'])->name('update');
            Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
            Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
        });
    });

    // Food Provider Routes
    Route::middleware(['role:FOOD'])->group(function () {
        Route::get('/food-provider/dashboard', function () {
            return view('dashboard.food', ['title' => 'Food Provider Dashboard']);
        })->name('food.dashboard');
    });

    // Laundry Provider Routes
    Route::middleware(['role:LAUNDRY'])->group(function () {
        Route::get('/laundry-provider/dashboard', function () {
            return view('dashboard.laundry', ['title' => 'Laundry Provider Dashboard']);
        })->name('laundry.dashboard');
    });
});