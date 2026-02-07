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
use App\Http\Controllers\RentalSearchController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    
    // ============ VERIFIED USER ROUTES ============
    Route::middleware('verified')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/user/dashboard', function () {
            return view('dashboard.user', ['title' => 'User Dashboard']);
        })->name('dashboard.user');

        // ============ USER SERVICE ROUTES ============
    
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
    

 // ============ RENTAL ROUTES ============
    Route::prefix('rental')->name('rental.')->group(function () {
        // Search & Browse
        Route::get('/search', [RentalController::class, 'search'])->name('search');
        Route::get('/property/{property}', [RentalController::class, 'show'])->name('property.details');
        
        // Booking Process
        Route::get('/apartment/{property}/rent', [RentalController::class, 'rentApartment'])->name('apartment.rent');
        Route::post('/apartment/{property}/book', [BookingController::class, 'storeApartment'])->name('apartment.book');
        
        Route::get('/hostel/{property}/room/{room}/book', [RentalController::class, 'bookRoom'])->name('room.book');
        Route::post('/hostel/{property}/room/{room}/book', [BookingController::class, 'storeRoom'])->name('room.book.submit');
        
        // Availability Check
        Route::post('/property/{property}/check-availability', [RentalController::class, 'checkAvailability'])
            ->name('check.availability');
        
        // My Bookings
        Route::get('/my-bookings', [BookingController::class, 'index'])->name('my-bookings');
        Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.details');
        Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        
        // Reviews
        Route::post('/property/{property}/review', [RentalController::class, 'storeReview'])->name('review.store');
        Route::get('/booking/{booking}/payment', [PaymentController::class, 'create'])->name('booking.payment');
        Route::post('/booking/{booking}/payment', [PaymentController::class, 'store'])->name('booking.payment.store');
        Route::get('/payment/{payment}/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
            // My Rental Dashboard
    Route::get('/rental', [RentalController::class, 'myRental'])->name('rental.index');
    
    // Rental Actions
    Route::post('/booking/{booking}/check-in', [RentalController::class, 'checkIn'])->name('booking.check-in');
    Route::post('/booking/{booking}/check-out', [RentalController::class, 'checkOut'])->name('booking.check-out');
    Route::post('/booking/{booking}/complaint', [RentalController::class, 'storeComplaint'])->name('booking.complaint.store');
    
    // Reviews
    Route::get('/booking/{booking}/review', [RentalController::class, 'createReview'])->name('booking.review.create');
    Route::post('/booking/{booking}/review', [RentalController::class, 'storeReview'])->name('booking.review.store');
    });
    }); // End of 'verified' middleware group
}); 

// ============ ROLE-SPECIFIC ROUTES ============
// These are OUTSIDE the 'verified' group so owners can access them without email verification
// Or keep them inside if you want verification required

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
        Route::get('/complaints', function () {
            return view('owner.pages.complaints.index');
        })->name('complaints.index');
        
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
        
        // Property Management Routes
        // Instead of: Route::resource('properties', PropertyController::class);
        Route::resource('owner/properties', \App\Http\Controllers\Owner\PropertyController::class);
        // Explicit property routes:
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        // Additional property routes
        Route::post('/properties/{property}/status', [PropertyController::class, 'updateStatus'])
            ->name('properties.status');
        
        Route::get('/properties/{property}/analytics', [PropertyController::class, 'analytics'])
            ->name('properties.analytics');
        
        // Commission API route
       Route::get('/api/commission-rate/{type}', [App\Http\Controllers\Owner\PropertyController::class, 'getCommissionRate']);
        // Room Management Routes
        Route::prefix('properties/{property}/rooms')->name('rooms.')->group(function () {
            Route::get('/create', [RoomController::class, 'create'])->name('create');
            Route::post('/', [RoomController::class, 'store'])->name('store');
            Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
            Route::put('/{room}', [RoomController::class, 'update'])->name('update');
            Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
            Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
        });
        // Rental Routes - FIX THIS SECTION
        Route::prefix('rental')->name('rental.')->group(function () {
            Route::get('/search', [RentalController::class, 'search'])->name('search');
            Route::get('/property/{property}', [RentalController::class, 'show'])->name('property.details');
            
            // Apartment Booking - SEPARATE GET and POST routes
            Route::get('/apartment/{property}/rent', [RentalController::class, 'rentApartment'])->name('apartment.rent');
            Route::post('/apartment/{property}/book', [BookingController::class, 'storeApartment'])->name('apartment.book');
            
            // Room Booking - SEPARATE GET and POST routes  
            Route::get('/hostel/{property}/room/{room}/book', [RentalController::class, 'bookRoom'])->name('room.book');
            Route::post('/hostel/{property}/room/{room}/book', [BookingController::class, 'storeRoom'])->name('room.book.submit');
            
            // Other routes...
        });
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
        Route::get('/dashboard', function () {
            return view('dashboard.laundry', ['title' => 'Laundry Provider Dashboard']);
        })->name('dashboard');
        
        // Add more laundry provider routes as needed
    });
});

// SUPERADMIN ROUTES
Route::middleware(['auth', 'role:SUPERADMIN'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.admin', ['title' => 'SuperAdmin Dashboard']);
        })->name('dashboard');
        
        // Add admin routes here
    });
});