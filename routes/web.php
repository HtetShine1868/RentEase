<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============ PUBLIC ROUTES ============
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware('auth')->group(function () {
    // Custom verification routes
    Route::prefix('verify')->name('verification.')->group(function () {
        Route::get('/', [VerificationController::class, 'show'])->name('show');
        Route::post('/', [VerificationController::class, 'verify'])->name('verify');
        Route::post('/resend', [VerificationController::class, 'resend'])->name('resend');
    });
    
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // ============ VERIFIED USER ROUTES ============
    Route::middleware('verified.custom')->group(function () {
        // Main Dashboard Route
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Regular User Dashboard (fallback)
        Route::get('/dashboard/user', function () {
            return view('dashboard.user', ['title' => 'User Dashboard']);
        })->name('dashboard.user');

        // ============ USER SERVICE ROUTES ============
    // Food Services
    Route::prefix('food')->group(function () {
        Route::get('/', [FoodServiceController::class, 'index'])->name('food.index');
        Route::get('/restaurants', [FoodServiceController::class, 'restaurants'])->name('food.restaurants');
        Route::get('/restaurants/{id}', [FoodServiceController::class, 'restaurant'])->name('food.restaurant.show');
        Route::get('/orders', [FoodServiceController::class, 'orders'])->name('food.orders');
        Route::get('/orders/{id}', [FoodServiceController::class, 'orderDetails'])->name('food.order.show');
        Route::get('/subscriptions', [FoodServiceController::class, 'subscriptions'])->name('food.subscriptions');
        Route::get('/subscriptions/create', [FoodServiceController::class, 'createSubscription'])->name('food.subscriptions.create');
        Route::post('/orders', [FoodServiceController::class, 'placeOrder'])->name('food.orders.place');
        Route::post('/subscriptions', [FoodServiceController::class, 'storeSubscription'])->name('food.subscriptions.store');
        Route::post('/orders/{id}/cancel', [FoodServiceController::class, 'cancelOrder'])->name('food.orders.cancel');
        Route::post('/subscriptions/{id}/pause', [FoodServiceController::class, 'pauseSubscription'])->name('food.subscriptions.pause');
        Route::post('/subscriptions/{id}/cancel', [FoodServiceController::class, 'cancelSubscription'])->name('food.subscriptions.cancel');
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
    
        // ============ UPDATED RENTAL ROUTES ============
        // My Rental Dashboard - Main Entry Point
        Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
        
        // Booking Actions
        Route::post('/bookings/{booking}/check-in', [RentalController::class, 'checkInBooking'])->name('bookings.check-in');
        Route::post('/bookings/{booking}/check-out', [RentalController::class, 'checkOutBooking'])->name('bookings.check-out');
        Route::post('/bookings/extend', [RentalController::class, 'extendBooking'])->name('bookings.extend');
        
        // Reviews
        Route::post('/reviews', [RentalController::class, 'submitReview'])->name('property-ratings.store');
        
        // Complaints
        Route::post('/complaints', [RentalController::class, 'submitComplaint'])->name('complaints.store');
        
        // Booking Management
        Route::get('/bookings/{booking}', [RentalController::class, 'showBooking'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [RentalController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::get('/bookings/{booking}/invoice', [RentalController::class, 'showInvoice'])->name('bookings.invoice');
        
        // Complaints Management
        Route::get('/complaints', [RentalController::class, 'complaints'])->name('complaints.index');
        Route::get('/complaints/{complaint}', [RentalController::class, 'showComplaint'])->name('complaints.show');
        
        // ============ PROPERTY SEARCH & BOOKING ROUTES ============
        Route::prefix('properties')->name('properties.')->group(function () {
            // Search & Browse
            Route::get('/search', [RentalController::class, 'search'])->name('search');
            Route::get('/{property}', [RentalController::class, 'show'])->name('show');
            
            // Booking Process
            Route::get('/{property}/rent', [RentalController::class, 'rentApartment'])->name('rent');
            Route::get('/{property}/rooms/{room}/book', [RentalController::class, 'bookRoom'])->name('rooms.book');
            
            // Availability Check
            Route::post('/{property}/check-availability', [RentalController::class, 'checkAvailability'])
                ->name('check-availability');
        });
        
        // ============ BOOKING CONTROLLER ROUTES (for actual booking creation) ============
        Route::prefix('bookings')->name('bookings.')->group(function () {
            // Create bookings
            Route::post('/apartment/{property}', [BookingController::class, 'storeApartment'])->name('apartment.store');
            Route::post('/room/{property}/{room}', [BookingController::class, 'storeRoom'])->name('room.store');
            
            // User's bookings
            Route::get('/my-bookings', [BookingController::class, 'index'])->name('my-bookings');
        });
        
        // ============ PAYMENT ROUTES ============
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/booking/{booking}', [PaymentController::class, 'create'])->name('create');
            Route::post('/booking/{booking}', [PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}/success', [PaymentController::class, 'success'])->name('success');
            Route::get('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        });
    }); // End of 'verified.custom' middleware group
}); 

// ============ ROLE-SPECIFIC ROUTES ============
// These are OUTSIDE the 'verified' group to allow access without email verification
// (You can move them inside the 'verified.custom' group if you want verification required)

// OWNER ROUTES
Route::middleware(['auth', 'role:OWNER'])->group(function () {
    Route::prefix('owner')->name('owner.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('owner.pages.dashboard');
        })->name('dashboard');
        
        // Bookings
        Route::get('/bookings', function () {
            return view('owner.pages.bookings.index');
        })->name('bookings.index');
        
        // Earnings
        Route::get('/earnings', function () {
            return view('owner.pages.earnings.index');
        })->name('earnings.index');
        
        // Complaints
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Owner\ComplaintController::class, 'index'])
                ->name('index');
            
            Route::get('/statistics', [\App\Http\Controllers\Owner\ComplaintController::class, 'statistics'])
                ->name('statistics');
            
            Route::get('/{complaint}', [\App\Http\Controllers\Owner\ComplaintController::class, 'show'])
                ->name('show');
            
            Route::post('/{complaint}/reply', [\App\Http\Controllers\Owner\ComplaintController::class, 'reply'])
                ->name('reply');
            
            Route::put('/{complaint}/status', [\App\Http\Controllers\Owner\ComplaintController::class, 'updateStatus'])
                ->name('update-status');
        });
        
        // Notifications
        Route::get('/notifications', function () {
            return view('owner.pages.notifications');
        })->name('notifications');
        
        // Settings
        Route::get('/settings', function () {
            return view('owner.pages.settings.index');
        })->name('settings.index');
        
        // Profile
        Route::get('/profile', function () {
            return view('owner.pages.profile');
        })->name('profile');
        
        // ============ OWNER PROPERTY MANAGEMENT ROUTES ============
        // Property Management Routes - CORRECTED with proper prefix
        Route::prefix('properties')->name('properties.')->group(function () {
            Route::get('/', [PropertyController::class, 'index'])->name('index');
            Route::get('/create', [PropertyController::class, 'create'])->name('create');
            Route::post('/', [PropertyController::class, 'store'])->name('store');
            Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
            Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
            Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
            Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
            
            // Additional property routes
            Route::post('/{property}/status', [PropertyController::class, 'updateStatus'])
                ->name('status');
            
            Route::get('/{property}/analytics', [PropertyController::class, 'analytics'])
                ->name('analytics');
            
            // Room Management Routes
            Route::prefix('{property}/rooms')->name('rooms.')->group(function () {
                Route::get('/create', [RoomController::class, 'create'])->name('create');
                Route::post('/', [RoomController::class, 'store'])->name('store');
                Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
                Route::put('/{room}', [RoomController::class, 'update'])->name('update');
                Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
                Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
            });
        });
        
        // Commission API route
        Route::get('/api/commission-rate/{type}', [PropertyController::class, 'getCommissionRate']);
    });
});

// FOOD PROVIDER ROUTES
Route::middleware(['auth', 'role:FOOD'])->group(function () {
    Route::prefix('food-provider')->name('food-provider.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('food-provider.dashboard.index', ['title' => 'Food Provider Dashboard']);
        })->name('dashboard');
        
        // Menu Routes
        Route::get('/menu', function () {
            return view('food-provider.menu.index');
        })->name('menu.index');

        Route::prefix('menu/items')->name('menu.items.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-status', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/export', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'export'])->name('export');
            Route::post('/reset-sold', [\App\Http\Controllers\FoodProvider\MenuItemController::class, 'resetSoldToday'])->name('reset-sold');
        });
        
        // Orders & Subscriptions
        Route::get('/orders', function () {
            return view('food-provider.orders.index');
        })->name('orders.index');
        
        Route::get('/subscriptions', function () {
            return view('food-provider.subscriptions.index');
        })->name('subscriptions.index');
        
        // Earnings & Reviews
        Route::get('/earnings', function () {
            return view('food-provider.earnings.index');
        })->name('earnings.index');
        
        Route::get('/reviews', function () {
            return view('food-provider.reviews.index');
        })->name('reviews.index');
        
        // Notifications & Settings
        Route::get('/notifications', function () {
            return view('food-provider.notifications.index');
        })->name('notifications.index');
        
        Route::get('/settings', function () {
            return view('food-provider.settings.index');
        })->name('settings.index');
        
        // Profile
        Route::get('/profile', [\App\Http\Controllers\FoodProvider\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit', [\App\Http\Controllers\FoodProvider\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [\App\Http\Controllers\FoodProvider\ProfileController::class, 'update'])->name('profile.update');
    });
});

// LAUNDRY PROVIDER ROUTES
Route::middleware(['auth', 'role:LAUNDRY'])->group(function () {
    Route::prefix('laundry-provider')->name('laundry-provider.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard.laundry', ['title' => 'Laundry Provider Dashboard']);
        })->name('dashboard');
        
        // Add more laundry provider routes as needed
    });
});

// SUPERADMIN ROUTES
Route::middleware(['auth', 'role:SUPERADMIN'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard.admin', ['title' => 'SuperAdmin Dashboard']);
        })->name('dashboard');
        
        // Add admin routes here
    });
});

// ============ FALLBACK FOR MISSING ROUTES ============
// Add this to prevent RouteNotFoundException
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});