<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RentalSearchController;
use App\Http\Controllers\VerificationController;
 use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/test-role-middleware', function () {
    return 'Middleware is working!';
})->middleware('role:FOOD');

// ============ PUBLIC ROUTES ============
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware('auth')->group(function () {
    // Custom Verification Routes (Unique names)
    Route::prefix('verify-email')->name('verify.')->group(function () {
        Route::get('/', [VerificationController::class, 'show'])->name('show');
        Route::post('/', [VerificationController::class, 'verify'])->name('submit');
        Route::post('/resend', [VerificationController::class, 'resend'])->name('resend');
    });
    
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ============ VERIFIED USER ROUTES ============
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/user/dashboard', function () {
        return view('dashboard.user', ['title' => 'User Dashboard']);
    })->name('dashboard.user');

    // ============ USER SERVICE ROUTES ============
    
    // Main rental page
    Route::get('/rental', function () {
        return view('rental.index');
    })->name('rental.index');

    // Food Services
    Route::prefix('food')->name('food.')->group(function () {
        Route::get('/', function () {
            return view('food.index');
        })->name('index');
        
        Route::get('/orders', function () {
            return view('food.orders');
        })->name('orders');
        
        Route::get('/subscriptions', function () {
            return view('food.subscriptions');
        })->name('subscriptions');
    });

    // Laundry Services
    Route::prefix('laundry')->name('laundry.')->group(function () {
        Route::get('/', function () {
            return view('laundry.index');
        })->name('index');
        
        Route::get('/orders', function () {
            return view('laundry.orders');
        })->name('orders');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', function () {
            return view('payments.index');
        })->name('index');
        
        Route::get('/history', function () {
            return view('payments.history');
        })->name('history');
    });

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        
        Route::get('/address/edit', [ProfileController::class, 'editAddress'])->name('address.edit');
        Route::put('/address/update', [ProfileController::class, 'updateAddress'])->name('address.update');
        Route::delete('/address/{id}', [ProfileController::class, 'deleteAddress'])->name('address.delete');
        Route::post('/address/{id}/set-default', [ProfileController::class, 'setDefaultAddress'])->name('address.set-default');
        
        Route::get('/password/edit', [ProfileController::class, 'editPassword'])->name('password.edit');
        Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Role Application Routes
    Route::prefix('role/apply')->name('role.apply.')->group(function () {
        Route::get('/', [RoleApplicationController::class, 'index'])->name('index');
        Route::get('/{roleType}', [RoleApplicationController::class, 'create'])->name('create');
        Route::post('/{roleType}', [RoleApplicationController::class, 'store'])->name('store');
        Route::get('/show/{id}', [RoleApplicationController::class, 'show'])->name('show');
        Route::delete('/{id}', [RoleApplicationController::class, 'destroy'])->name('destroy');
    });

    // Rental Search Routes
    Route::prefix('rental')->name('rental.')->group(function () {
        Route::get('/search', [RentalSearchController::class, 'index'])->name('search');
        Route::get('/property/{property}', [RentalSearchController::class, 'show'])->name('property.details');
        Route::get('/property/{property}/room/{room}', [RentalSearchController::class, 'showRoom'])->name('room.details');
        Route::get('/property/{property}/rent', [RentalSearchController::class, 'rentApartment'])->name('apartment.rent');
        Route::get('/property/{property}/room/{room}/rent', [RentalSearchController::class, 'rentRoom'])->name('room.rent');
    });

    // ============ ROLE-SPECIFIC ROUTES ============
    
    // Owner Routes
    Route::middleware(['role:OWNER'])->group(function () {
        Route::resource('properties', PropertyController::class);
        Route::post('/properties/{property}/status', [PropertyController::class, 'updateStatus'])->name('properties.status');
        Route::get('/properties/{property}/analytics', [PropertyController::class, 'analytics'])->name('properties.analytics');
        
        Route::prefix('properties/{property}/rooms')->name('rooms.')->group(function () {
            Route::get('/create', [RoomController::class, 'create'])->name('create');
            Route::post('/', [RoomController::class, 'store'])->name('store');
            Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
            Route::put('/{room}', [RoomController::class, 'update'])->name('update');
            Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
            Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
        });   
          }); 

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.pages.dashboard');
    })->name('dashboard');
    
    Route::get('/properties', function () {
        return view('owner.pages.properties.index');
    })->name('properties.index');
    
    Route::get('/bookings', function () {
        return view('owner.pages.bookings.index');
    })->name('bookings.index');
    
    Route::get('/earnings', function () {
        return view('owner.pages.earnings.index');
    })->name('earnings.index');
    
    Route::get('/complaints', function () {
        return view('owner.pages.complaints.index');
    })->name('complaints.index');
    
    Route::get('/notifications', function () {
        return view('owner.pages.notification');
    })->name('notifications');
    
    Route::get('/settings', function () {
        return view('owner.pages.settings.index');
    })->name('settings.index');
    
    Route::get('/profile', function () {
        return view('owner.pages.profile');
    })->name('profile');

    Route::get('/owner/notifications', function () {
    return view('owner.pages.notifications');
})->name('owner.notifications');
});
    });

    // SuperAdmin Routes
    Route::middleware(['role:SUPERADMIN'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboard.admin', ['title' => 'SuperAdmin Dashboard']);
        })->name('admin.dashboard');
    });

    // Food Provider Routes
    Route::middleware(['role:FOOD'])->group(function () {
        Route::get('/food-provider/dashboard', function () {
            return view('food-provider.dashboard.index', ['title' => 'Food Provider Dashboard']);
        })->name('food.dashboard');
    });

    // Laundry Provider Routes
    Route::middleware(['role:LAUNDRY'])->group(function () {
        Route::get('/laundry-provider/dashboard', function () {
            return view('dashboard.laundry', ['title' => 'Laundry Provider Dashboard']);
        })->name('laundry.dashboard');
    });
  