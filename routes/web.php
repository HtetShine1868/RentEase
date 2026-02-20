<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\ComplaintController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodApiController;
use App\Http\Controllers\FoodServiceController;
use App\Http\Controllers\FoodRatingController;
use App\Http\Controllers\FoodProvider\MenuItemController;
use App\Http\Controllers\FoodProvider\ReviewController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
    // For showing the forgot password form
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

// For handling the reset link submission
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// For showing the reset password form (when user clicks email link)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

// For handling the new password submission
Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware('auth')->group(function () {
    Route::prefix('verify')->name('verification.')->group(function () {
        Route::get('/', [App\Http\Controllers\VerificationController::class, 'show'])->name('show');
        Route::post('/', [App\Http\Controllers\VerificationController::class, 'verify'])->name('verify');
        Route::post('/resend', [App\Http\Controllers\VerificationController::class, 'resend'])->name('resend');
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
    Route::get('/food', [FoodController::class, 'index'])->name('food.index');
    
    // API endpoints for AJAX calls
    Route::prefix('food/api')->name('food.api.')->group(function () {
        Route::get('/restaurants', [FoodApiController::class, 'getRestaurants'])->name('restaurants');
        Route::get('/restaurant/{id}/menu', [FoodApiController::class, 'getRestaurantMenu'])->name('restaurant.menu');
        Route::get('/restaurant/{id}/ratings', [FoodApiController::class, 'getRestaurantRatings'])->name('restaurant.ratings');
        Route::get('/orders', [FoodApiController::class, 'getOrders'])->name('orders');
        Route::get('/subscriptions', [FoodApiController::class, 'getSubscriptions'])->name('subscriptions');
        Route::post('/order/place', [FoodApiController::class, 'placeOrder'])->name('order.place');
        Route::post('/order/{id}/cancel', [FoodApiController::class, 'cancelOrder'])->name('order.cancel');
        Route::post('/order/{id}/reorder', [FoodApiController::class, 'reorder'])->name('order.reorder');
        Route::post('/subscription/create', [FoodApiController::class, 'createSubscription'])->name('subscription.create');
        Route::post('/subscription/{id}/cancel', [FoodApiController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::post('/subscription/{id}/pause', [FoodApiController::class, 'pauseSubscription'])->name('subscription.pause');
        Route::post('/subscription/{id}/resume', [FoodApiController::class, 'resumeSubscription'])->name('subscription.resume');
    });
    
    // Rating Routes (web views, not API)
    Route::prefix('food/orders/rate')->name('food.rate.')->group(function () {
        Route::get('/{order}', [FoodRatingController::class, 'show'])->name('show');
        Route::post('/{order}', [FoodRatingController::class, 'store'])->name('store');
        Route::get('/{order}/edit', [FoodRatingController::class, 'edit'])->name('edit');
        Route::put('/{order}', [FoodRatingController::class, 'update'])->name('update');
        Route::delete('/{order}', [FoodRatingController::class, 'destroy'])->name('destroy');
    });
    
    // My ratings page
    Route::get('/food/my-ratings', [FoodRatingController::class, 'myRatings'])->name('food.ratings.my');

    // Laundry routes
    Route::get('/laundry', [App\Http\Controllers\LaundryController::class, 'index'])->name('laundry.index');
    
    // Laundry API routes
    Route::prefix('laundry/api')->name('laundry.api.')->group(function () {
        Route::get('/providers', [App\Http\Controllers\LaundryApiController::class, 'getProviders'])->name('providers');
        Route::get('/provider/{id}/items', [App\Http\Controllers\LaundryApiController::class, 'getProviderItems'])->name('provider.items');
        Route::get('/orders', [App\Http\Controllers\LaundryApiController::class, 'getOrders'])->name('orders');
        Route::get('/order/{id}', [App\Http\Controllers\LaundryApiController::class, 'getOrder'])->name('order');
        Route::post('/order/place', [App\Http\Controllers\LaundryApiController::class, 'placeOrder'])->name('order.place');
        Route::post('/order/{id}/cancel', [App\Http\Controllers\LaundryApiController::class, 'cancelOrder'])->name('order.cancel');
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
           // Main notifications page
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
    
    // AJAX routes for notifications
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])
        ->name('notifications.unread-count');
    
    Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])
        ->name('notifications.recent');
    
    // Mark single notification as read
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-read');
    
    // Mark all notifications as read
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    
    // Delete single notification
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    
    // Clear all notifications
    Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])
        ->name('notifications.clear-all');

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

        // My Rental Dashboard - Main Entry Point
        Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
        
        // Booking Actions
        Route::post('/bookings/{booking}/check-in', [RentalController::class, 'checkInBooking'])->name('bookings.check-in');
        Route::post('/bookings/{booking}/check-out', [RentalController::class, 'checkOutBooking'])->name('bookings.check-out');
        Route::post('/bookings/extend', [RentalController::class, 'extendBooking'])->name('bookings.extend');
        Route::get('/bookings/{booking}/manage', [BookingController::class, 'manage'])->name('bookings.manage');
        Route::get('/rental/booking/{booking}', [RentalController::class, 'showBooking'])->name('rental.booking-details');
        // Reviews
        Route::post('/reviews', [RentalController::class, 'submitReview'])->name('property-ratings.store');
        
        // Complaints (USER complaints)
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [RentalController::class, 'complaints'])->name('index');
            Route::post('/', [RentalController::class, 'submitComplaint'])->name('store');
            Route::get('/{complaint}', [RentalController::class, 'showComplaint'])->name('show');
        });
        
        // Booking Management
        Route::get('/bookings/{booking}', [RentalController::class, 'showBooking'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [RentalController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::get('/bookings/{booking}/invoice', [RentalController::class, 'showInvoice'])->name('bookings.invoice');
        
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
    }); 
}); 

// ============ ROLE-SPECIFIC ROUTES ============
// OWNER ROUTES
Route::middleware(['auth', 'role:OWNER'])->group(function () {
    Route::prefix('owner')->name('owner.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('owner.pages.dashboard');
        })->name('dashboard');
        
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Owner\BookingController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Owner\BookingController::class, 'show'])->name('show');
            Route::put('/{id}/status', [\App\Http\Controllers\Owner\BookingController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/reminder', [\App\Http\Controllers\Owner\BookingController::class, 'sendReminder'])->name('send-reminder');
            Route::get('/export', [\App\Http\Controllers\Owner\BookingController::class, 'export'])->name('export');
        });
        
        // Earnings
        Route::get('/earnings', function () {
            return view('owner.pages.earnings.index');
        })->name('earnings.index');
        
        // ============ COMPLAINT ROUTES (FIXED) ============
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::post('/{id}/assign-self', [ComplaintController::class, 'assignToSelf'])->name('assign-self');
            Route::put('/{id}/status', [ComplaintController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/reply', [ComplaintController::class, 'sendReply'])->name('reply');
            Route::get('/export', [ComplaintController::class, 'export'])->name('export');
            Route::get('/statistics', [ComplaintController::class, 'statistics'])->name('statistics');
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
        // Property Management Routes
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
        
        // Order Routes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\OrderController::class, 'index'])
                ->name('index');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\OrderController::class, 'show'])
                ->name('show');
            Route::patch('/{id}/status', [\App\Http\Controllers\FoodProvider\OrderController::class, 'updateStatus'])
                ->name('update-status');
            Route::post('/bulk-status', [\App\Http\Controllers\FoodProvider\OrderController::class, 'bulkUpdateStatus'])
                ->name('bulk-status');
            Route::get('/export', [\App\Http\Controllers\FoodProvider\OrderController::class, 'export'])
                ->name('export');
            Route::get('/{id}/print', [\App\Http\Controllers\FoodProvider\OrderController::class, 'printInvoice'])
                ->name('print');
        });

        // Food Provider Subscription Routes
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'index'])
                ->name('index');
            Route::get('/today', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'todayDeliveries'])
                ->name('today');
            Route::post('/generate-today', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'generateTodayOrders'])
                ->name('generate-today');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'show'])
                ->name('show');
            Route::post('/{id}/status', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'updateStatus'])
                ->name('update-status');
            Route::get('/statistics/data', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'statistics'])
                ->name('statistics');
        });
        
        // Food Provider Review Routes
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\ReviewController::class, 'index'])
                ->name('index');
            Route::get('/export', [\App\Http\Controllers\FoodProvider\ReviewController::class, 'export'])
                ->name('export');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\ReviewController::class, 'show'])
                ->name('show');
            Route::post('/{id}/reply', [\App\Http\Controllers\FoodProvider\ReviewController::class, 'reply'])
                ->name('reply');
        });
        
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

// Laundry Provider Routes
Route::middleware(['auth', 'role:LAUNDRY'])->prefix('laundry-provider')->name('laundry-provider.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\OrderController::class, 'index'])
            ->name('index');
        Route::get('/rush', [App\Http\Controllers\LaundryProvider\OrderController::class, 'rushOrders'])
            ->name('rush');
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\OrderController::class, 'show'])
            ->name('show');
        Route::post('/{id}/status', [App\Http\Controllers\LaundryProvider\OrderController::class, 'updateStatus'])
            ->name('update-status');
        Route::post('/bulk-update', [App\Http\Controllers\LaundryProvider\OrderController::class, 'bulkUpdate'])
            ->name('bulk-update');
        Route::post('/{id}/return-date', [App\Http\Controllers\LaundryProvider\OrderController::class, 'updateReturnDate'])
            ->name('update-return-date');
        Route::get('/export', [App\Http\Controllers\LaundryProvider\OrderController::class, 'export'])
            ->name('export');
        Route::get('/{id}/print', [App\Http\Controllers\LaundryProvider\OrderController::class, 'printInvoice'])
            ->name('print');
    });
    
    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'index'])
            ->name('index');
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'show'])
            ->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'reply'])
            ->name('reply');
        Route::get('/export', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'export'])
            ->name('export');
    });
    
    // Notifications
    Route::get('/notifications', function () {
        return view('laundry-provider.notifications');
    })->name('notifications');
    
    // Profile
    Route::get('/profile', function () {
        return view('laundry-provider.profile');
    })->name('profile');
    
    // Settings
    Route::get('/settings', function () {
        return view('laundry-provider.settings');
    })->name('settings');
});

        // SUPERADMIN ROUTES
        Route::middleware(['auth', 'role:SUPERADMIN'])->prefix('admin')->name('admin.')->group(function () {
            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
                ->name('dashboard');
            
            // Role Applications (with tabs)
            Route::prefix('role-applications')->name('role-applications.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\RoleApplicationController::class, 'index'])
                    ->name('index');
                Route::get('/export', [App\Http\Controllers\Admin\RoleApplicationController::class, 'export'])
                    ->name('export');
                Route::get('/statistics', [App\Http\Controllers\Admin\RoleApplicationController::class, 'statistics'])
                    ->name('statistics');
                Route::get('/{id}', [App\Http\Controllers\Admin\RoleApplicationController::class, 'show'])
                    ->name('show');
                Route::get('/{id}/review', [App\Http\Controllers\Admin\RoleApplicationController::class, 'review'])
                    ->name('review');
                Route::post('/{id}/approve', [App\Http\Controllers\Admin\RoleApplicationController::class, 'approve'])
                    ->name('approve');
                Route::post('/{id}/reject', [App\Http\Controllers\Admin\RoleApplicationController::class, 'reject'])
                    ->name('reject');
                Route::get('/{id}/download-document', [App\Http\Controllers\Admin\RoleApplicationController::class, 'downloadDocument'])
                    ->name('download-document');
                Route::post('/bulk-approve', [App\Http\Controllers\Admin\RoleApplicationController::class, 'bulkApprove'])
                    ->name('bulk-approve');
            });
            
            // Commissions (Admin Only)
            Route::prefix('commissions')->name('commissions.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\CommissionController::class, 'index'])
                    ->name('index');
                Route::post('/update', [App\Http\Controllers\Admin\CommissionController::class, 'update'])
                    ->name('update');
                Route::get('/rate/{type}', [App\Http\Controllers\Admin\CommissionController::class, 'getRate'])
                    ->name('rate');
                Route::post('/calculate', [App\Http\Controllers\Admin\CommissionController::class, 'calculate'])
                    ->name('calculate');
                Route::post('/reset', [App\Http\Controllers\Admin\CommissionController::class, 'reset'])
                    ->name('reset');
            });
            // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])
                ->name('index');
                Route::get('/export', [App\Http\Controllers\Admin\UserController::class, 'export'])
                    ->name('export');
                Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])
                    ->name('show');
                Route::post('/{id}/status', [App\Http\Controllers\Admin\UserController::class, 'updateStatus'])
                    ->name('update-status');
                Route::post('/{id}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])
                    ->name('assign-role');
                Route::post('/{id}/remove-role', [App\Http\Controllers\Admin\UserController::class, 'removeRole'])
                    ->name('remove-role');
                Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])
                    ->name('destroy');
    });
        });

// ============ FALLBACK FOR MISSING ROUTES ============
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->methods(['get']);