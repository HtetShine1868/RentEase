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
use App\Http\Controllers\FoodRatingController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\FoodProvider\MenuItemController;
use App\Http\Controllers\FoodProvider\ReviewController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\RentalChatController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// TEMPORARY TEST ROUTE - Add this after your orders group
Route::post('/laundry-provider/test-post', function() {
    return response()->json(['success' => true, 'message' => 'POST test works!']);
});
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============ PUBLIC ROUTES ============
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware('auth')->group(function () {
    Route::prefix('verify')->name('verification.')->group(function () {
        Route::get('/', [App\Http\Controllers\VerificationController::class, 'show'])->name('show');
        Route::post('/', [App\Http\Controllers\VerificationController::class, 'verify'])->name('verify');
        Route::post('/resend', [App\Http\Controllers\VerificationController::class, 'resend'])->name('resend');
    });
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // ============ VERIFIED USER ROUTES ============
    Route::middleware('verified.custom')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/user', function () {
            return view('dashboard.user', ['title' => 'User Dashboard']);
        })->name('dashboard.user');

        // Food Services
        Route::get('/food', [FoodController::class, 'index'])->name('food.index');
        
        // Food API
        Route::prefix('food/api')->name('food.api.')->group(function () {
            Route::get('/restaurants', [FoodApiController::class, 'getRestaurants'])->name('restaurants');
            Route::get('/restaurant/{id}/menu', [FoodApiController::class, 'getRestaurantMenu'])->name('restaurant.menu');
            Route::get('/restaurant/{id}/ratings', [FoodApiController::class, 'getRestaurantRatings'])->name('restaurant.ratings');
            Route::get('/nearby-restaurants', [FoodApiController::class, 'getNearbyRestaurants'])->name('nearby');
            Route::get('/reverse-geocode', [FoodApiController::class, 'reverseGeocode'])->name('reverse-geocode');
            Route::get('/search-locations', [FoodApiController::class, 'searchLocations'])->name('search-locations');
            Route::get('/user/addresses', [FoodApiController::class, 'getUserAddresses'])->name('addresses');
            Route::post('/user/addresses', [FoodApiController::class, 'saveAddress'])->name('addresses.save');
            Route::get('/orders', [FoodApiController::class, 'getOrders'])->name('orders');
            Route::post('/order/place', [FoodApiController::class, 'placeOrder'])->name('order.place');
            Route::post('/order/{id}/cancel', [FoodApiController::class, 'cancelOrder'])->name('order.cancel');
            Route::post('/order/{id}/reorder', [FoodApiController::class, 'reorder'])->name('order.reorder');
            Route::get('/subscriptions', [FoodApiController::class, 'getSubscriptions'])->name('subscriptions');
            Route::post('/subscription/create', [FoodApiController::class, 'createSubscription'])->name('subscription.create');
            Route::post('/subscription/{id}/cancel', [FoodApiController::class, 'cancelSubscription'])->name('subscription.cancel');
            Route::post('/subscription/{id}/pause', [FoodApiController::class, 'pauseSubscription'])->name('subscription.pause');
            Route::post('/subscription/{id}/resume', [FoodApiController::class, 'resumeSubscription'])->name('subscription.resume');
            Route::post('/rating/{id}/helpful', [FoodApiController::class, 'markHelpful'])->name('rating.helpful');
        });
        
        // Food Rating Routes
        Route::prefix('food/orders/rate')->name('food.rate.')->group(function () {
            Route::get('/{order}', [FoodRatingController::class, 'show'])->name('show');
            Route::post('/{order}', [FoodRatingController::class, 'store'])->name('store');
            Route::get('/{order}/edit', [FoodRatingController::class, 'edit'])->name('edit');
            Route::put('/{order}', [FoodRatingController::class, 'update'])->name('update');
            Route::delete('/{order}', [FoodRatingController::class, 'destroy'])->name('destroy');
        });
        
        Route::get('/food/my-ratings', [FoodRatingController::class, 'myRatings'])->name('food.ratings.my');

        // ======= PUBLIC LAUNDRY ROUTES (for customers) =======
        Route::prefix('laundry')->name('laundry.')->group(function () {
            Route::get('/', [App\Http\Controllers\LaundryController::class, 'index'])->name('index');
            Route::get('/providers', [App\Http\Controllers\LaundryController::class, 'providers'])->name('providers');
            Route::get('/provider/{id}', [App\Http\Controllers\LaundryController::class, 'showProvider'])->name('provider.show');
            
            Route::middleware('auth')->group(function () {
                Route::get('/order/create/{providerId}', [App\Http\Controllers\LaundryController::class, 'createOrder'])->name('create-order');
                Route::post('/order/place', [App\Http\Controllers\LaundryController::class, 'placeOrder'])->name('place-order');
                Route::get('/order/{id}', [App\Http\Controllers\LaundryController::class, 'showOrder'])->name('order.show');
                Route::post('/order/{id}/cancel', [App\Http\Controllers\LaundryController::class, 'cancelOrder'])->name('order.cancel');
                Route::get('/order/{id}/rate', [App\Http\Controllers\LaundryController::class, 'rateOrder'])->name('rate.show');
                Route::post('/order/{id}/rate', [App\Http\Controllers\LaundryController::class, 'submitRating'])->name('rate.submit');
                Route::get('/my-orders', [App\Http\Controllers\LaundryController::class, 'myOrders'])->name('my-orders');
            });
        });

                // Rental Chat Routes
        Route::middleware(['auth'])->prefix('rental')->name('rental.')->group(function () {
            Route::get('/chats', [RentalChatController::class, 'index'])->name('chats');
            Route::get('/chat/{booking}', [RentalChatController::class, 'show'])->name('chat.show');
            Route::post('/chat/{booking}/send', [RentalChatController::class, 'sendMessage'])->name('chat.send');
            Route::get('/chat/{booking}/new', [RentalChatController::class, 'getNewMessages'])->name('chat.new');
            Route::get('/unread-count', [RentalChatController::class, 'getUnreadCount'])->name('unread-count');
            
            // ADD THIS ROUTE - Start a new chat from property page
            Route::get('/start/{property}', [RentalChatController::class, 'startFromProperty'])->name('chat.start');
        });
        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', function () { return view('payments.index'); })->name('index');
            Route::get('/history', function () { return view('payments.history'); })->name('history');
        });
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');

        // User Profile Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
            Route::get('/address', [App\Http\Controllers\ProfileController::class, 'address'])->name('address');
            Route::post('/address', [App\Http\Controllers\ProfileController::class, 'addAddress'])->name('address.add');
            Route::put('/address/{id}', [App\Http\Controllers\ProfileController::class, 'updateAddress'])->name('address.update');
            Route::delete('/address/{id}', [App\Http\Controllers\ProfileController::class, 'deleteAddress'])->name('address.delete');
            Route::post('/address/{id}/default', [App\Http\Controllers\ProfileController::class, 'setDefaultAddress'])->name('address.default');
            Route::get('/address/{id}', [App\Http\Controllers\ProfileController::class, 'getAddress'])->name('address.get');
            Route::get('/password', [App\Http\Controllers\ProfileController::class, 'passwordForm'])->name('password');
            Route::put('/password/update', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
            Route::post('/geocode', [App\Http\Controllers\ProfileController::class, 'geocode'])->name('geocode');
            Route::post('/reverse-geocode', [App\Http\Controllers\ProfileController::class, 'reverseGeocode'])->name('reverse-geocode');
        });

        // Role Application Routes
        Route::prefix('role/apply')->name('role.apply.')->group(function () {
            Route::get('/', [RoleApplicationController::class, 'index'])->name('index');
            Route::get('/{roleType}', [RoleApplicationController::class, 'create'])->name('create');
            Route::post('/{roleType}', [RoleApplicationController::class, 'store'])->name('store');
            Route::get('/show/{id}', [RoleApplicationController::class, 'show'])->name('show');
            Route::delete('/{id}', [RoleApplicationController::class, 'destroy'])->name('destroy');
        });

        // Rental Routes
        Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
        Route::post('/bookings/{booking}/check-in', [RentalController::class, 'checkInBooking'])->name('bookings.check-in');
        Route::post('/bookings/{booking}/check-out', [RentalController::class, 'checkOutBooking'])->name('bookings.check-out');
        Route::post('/bookings/extend', [RentalController::class, 'extendBooking'])->name('bookings.extend');
        Route::get('/bookings/{booking}/manage', [BookingController::class, 'manage'])->name('bookings.manage');
        Route::get('/rental/booking/{booking}', [RentalController::class, 'showBooking'])->name('rental.booking-details');
        Route::post('/reviews', [RentalController::class, 'submitReview'])->name('property-ratings.store');

        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [RentalController::class, 'complaints'])->name('index');
            Route::post('/', [RentalController::class, 'submitComplaint'])->name('store');
            Route::get('/{complaint}', [RentalController::class, 'showComplaint'])->name('show');
        });
        
        Route::get('/bookings/{booking}', [RentalController::class, 'showBooking'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [RentalController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::get('/bookings/{booking}/invoice', [RentalController::class, 'showInvoice'])->name('bookings.invoice');
        
        Route::prefix('properties')->name('properties.')->group(function () {
            Route::get('/search', [RentalController::class, 'search'])->name('search');
            Route::get('/{property}', [RentalController::class, 'show'])->name('show');
            Route::get('/{property}/rooms/{room}', [RentalController::class, 'showRoom'])->name('rooms.details');
            Route::get('/{property}/rent', [RentalController::class, 'rentApartment'])->name('rent');
            Route::get('/{property}/rooms/{room}/book', [RentalController::class, 'bookRoom'])->name('rooms.book');
            Route::post('/{property}/check-availability', [RentalController::class, 'checkAvailability'])->name('check-availability');
        });
        
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::post('/apartment/{property}', [BookingController::class, 'storeApartment'])->name('apartment.store');
            Route::post('/room/{property}/{room}', [BookingController::class, 'storeRoom'])->name('room.store');
            Route::get('/my-bookings', [BookingController::class, 'index'])->name('my-bookings');
        });
        
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/booking/{booking}', [PaymentController::class, 'create'])->name('create');
            Route::post('/booking/{booking}', [PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}/success', [PaymentController::class, 'success'])->name('success');
            Route::get('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        });
    }); 
}); 

// ============ OWNER ROUTES ============
Route::middleware(['auth', 'role:OWNER'])->group(function () {
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', function () { return view('owner.pages.dashboard'); })->name('dashboard');
        
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Owner\BookingController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Owner\BookingController::class, 'show'])->name('show');
            Route::put('/{id}/status', [\App\Http\Controllers\Owner\BookingController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/reminder', [\App\Http\Controllers\Owner\BookingController::class, 'sendReminder'])->name('send-reminder');
            Route::get('/export', [\App\Http\Controllers\Owner\BookingController::class, 'export'])->name('export');
        });
        
        Route::get('/earnings', function () { return view('owner.pages.earnings.index'); })->name('earnings.index');
        
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::post('/{id}/assign-self', [ComplaintController::class, 'assignToSelf'])->name('assign-self');
            Route::put('/{id}/status', [ComplaintController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/reply', [ComplaintController::class, 'sendReply'])->name('reply');
            Route::get('/export', [ComplaintController::class, 'export'])->name('export');
            Route::get('/statistics', [ComplaintController::class, 'statistics'])->name('statistics');
        });
        
        Route::get('/notifications', function () { return view('owner.pages.notifications'); })->name('notifications');
        Route::get('/settings', function () { return view('owner.pages.settings.index'); })->name('settings.index');
        Route::get('/profile', function () { return view('owner.pages.profile'); })->name('profile');
        Route::get('/chats', [App\Http\Controllers\Owner\ChatController::class, 'index'])->name('chats');
        Route::get('/chat/{booking}', [App\Http\Controllers\Owner\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{booking}/send', [App\Http\Controllers\Owner\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/{booking}/new', [App\Http\Controllers\Owner\ChatController::class, 'getNewMessages'])->name('chat.new');

        
        Route::prefix('properties')->name('properties.')->group(function () {
            Route::get('/', [PropertyController::class, 'index'])->name('index');
            Route::get('/create', [PropertyController::class, 'create'])->name('create');
            Route::post('/', [PropertyController::class, 'store'])->name('store');
            Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
            Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
            Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
            Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
            Route::post('/{property}/status', [PropertyController::class, 'updateStatus'])->name('status');
            Route::get('/{property}/analytics', [PropertyController::class, 'analytics'])->name('analytics');
            
            Route::prefix('{property}/rooms')->name('rooms.')->group(function () {
                Route::get('/create', [RoomController::class, 'create'])->name('create');
                Route::post('/', [RoomController::class, 'store'])->name('store');
                Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
                Route::put('/{room}', [RoomController::class, 'update'])->name('update');
                Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
                Route::post('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
            });
        });
        
        Route::get('/api/commission-rate/{type}', [PropertyController::class, 'getCommissionRate']);
    });
});

// ============ FOOD PROVIDER ROUTES ============
Route::middleware(['auth', 'role:FOOD'])->group(function () {
    Route::prefix('food-provider')->name('food-provider.')->group(function () {
        Route::get('/dashboard', function () { return view('food-provider.dashboard.index'); })->name('dashboard');
        Route::get('/menu', function () { return view('food-provider.menu.index'); })->name('menu.index');

        Route::prefix('menu/items')->name('menu.items.')->group(function () {
            Route::get('/', [MenuItemController::class, 'index'])->name('index');
            Route::get('/create', [MenuItemController::class, 'create'])->name('create');
            Route::post('/', [MenuItemController::class, 'store'])->name('store');
            Route::get('/{id}', [MenuItemController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [MenuItemController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MenuItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [MenuItemController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [MenuItemController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-status', [MenuItemController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/export', [MenuItemController::class, 'export'])->name('export');
            Route::post('/reset-sold', [MenuItemController::class, 'resetSoldToday'])->name('reset-sold');
        });
        
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\OrderController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [\App\Http\Controllers\FoodProvider\OrderController::class, 'updateStatus'])->name('update-status');
            Route::post('/bulk-status', [\App\Http\Controllers\FoodProvider\OrderController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/export', [\App\Http\Controllers\FoodProvider\OrderController::class, 'export'])->name('export');
            Route::get('/{id}/print', [\App\Http\Controllers\FoodProvider\OrderController::class, 'printInvoice'])->name('print');
        });

        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'index'])->name('index');
            Route::get('/today', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'todayDeliveries'])->name('today');
            Route::post('/generate-today', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'generateTodayOrders'])->name('generate-today');
            Route::get('/{id}', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'show'])->name('show');
            Route::post('/{id}/status', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'updateStatus'])->name('update-status');
            Route::get('/statistics/data', [\App\Http\Controllers\FoodProvider\SubscriptionController::class, 'statistics'])->name('statistics');
        });
        
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/export', [ReviewController::class, 'export'])->name('export');
            Route::get('/{id}', [ReviewController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [ReviewController::class, 'reply'])->name('reply');
        });
        
        Route::get('/notifications', function () { return view('food-provider.notifications.index'); })->name('notifications.index');
        Route::get('/settings', function () { return view('food-provider.settings.index'); })->name('settings.index');
        
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\FoodProvider\ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [App\Http\Controllers\FoodProvider\ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [App\Http\Controllers\FoodProvider\ProfileController::class, 'update'])->name('update');
            Route::get('/business', [App\Http\Controllers\FoodProvider\ProfileController::class, 'businessInfo'])->name('business');
            Route::put('/business/update', [App\Http\Controllers\FoodProvider\ProfileController::class, 'updateBusiness'])->name('business.update');
            Route::get('/address', [App\Http\Controllers\FoodProvider\ProfileController::class, 'address'])->name('address');
            Route::post('/address', [App\Http\Controllers\FoodProvider\ProfileController::class, 'addAddress'])->name('address.add');
            Route::put('/address/{id}', [App\Http\Controllers\FoodProvider\ProfileController::class, 'updateAddress'])->name('address.update');
            Route::delete('/address/{id}', [App\Http\Controllers\FoodProvider\ProfileController::class, 'deleteAddress'])->name('address.delete');
            Route::post('/address/{id}/default', [App\Http\Controllers\FoodProvider\ProfileController::class, 'setDefaultAddress'])->name('address.default');
            Route::get('/address/{id}', [App\Http\Controllers\FoodProvider\ProfileController::class, 'getAddress'])->name('address.get');
            Route::post('/business-location', [App\Http\Controllers\FoodProvider\ProfileController::class, 'updateBusinessLocation'])->name('business.location');
            Route::get('/password', [App\Http\Controllers\FoodProvider\ProfileController::class, 'passwordForm'])->name('password');
            Route::put('/password/update', [App\Http\Controllers\FoodProvider\ProfileController::class, 'updatePassword'])->name('password.update');
            Route::post('/geocode', [App\Http\Controllers\FoodProvider\ProfileController::class, 'geocode'])->name('geocode');
            Route::post('/reverse-geocode', [App\Http\Controllers\FoodProvider\ProfileController::class, 'reverseGeocode'])->name('reverse-geocode');
        });
    });
});

// ============ LAUNDRY PROVIDER ROUTES ============
Route::middleware(['auth', 'role:LAUNDRY'])->prefix('laundry-provider')->name('laundry-provider.')->group(function () {
    
    // ======= DASHBOARD =======
    Route::get('/dashboard', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'index'])->name('dashboard');
    
    // ======= ORDERS MANAGEMENT =======
    Route::prefix('orders')->name('orders.')->group(function () {
        // Main order pages
        Route::get('/', [App\Http\Controllers\LaundryProvider\OrderController::class, 'index'])->name('index');
        Route::get('/rush', [App\Http\Controllers\LaundryProvider\OrderController::class, 'rushOrders'])->name('rush');
        Route::get('/normal', [App\Http\Controllers\LaundryProvider\OrderController::class, 'normalOrders'])->name('normal');

        // Order operations
        Route::get('/filter', [App\Http\Controllers\LaundryProvider\OrderController::class, 'filter'])->name('filter');
        Route::get('/search', [App\Http\Controllers\LaundryProvider\OrderController::class, 'search'])->name('search');
        Route::get('/export', [App\Http\Controllers\LaundryProvider\OrderController::class, 'export'])->name('export');

        // Status update routes 
        Route::post('/{id}/accept', [App\Http\Controllers\LaundryProvider\OrderController::class, 'accept'])->name('accept');
        Route::post('/{id}/picked-up', [App\Http\Controllers\LaundryProvider\OrderController::class, 'markPickedUp'])->name('picked-up');
        Route::post('/{id}/start-processing', [App\Http\Controllers\LaundryProvider\OrderController::class, 'startProcessing'])->name('start-processing');
        Route::post('/{id}/mark-ready', [App\Http\Controllers\LaundryProvider\OrderController::class, 'markReady'])->name('mark-ready');
        Route::post('/{id}/out-for-delivery', [App\Http\Controllers\LaundryProvider\OrderController::class, 'outForDelivery'])->name('out-for-delivery');
        Route::post('/{id}/deliver', [App\Http\Controllers\LaundryProvider\OrderController::class, 'deliver'])->name('deliver');
        Route::post('/{id}/cancel', [App\Http\Controllers\LaundryProvider\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/assign', [App\Http\Controllers\LaundryProvider\OrderController::class, 'assignStaff'])->name('assign');
        Route::post('/{id}/reschedule', [App\Http\Controllers\LaundryProvider\OrderController::class, 'reschedule'])->name('reschedule');
        
        // Patch route
        Route::patch('/{id}/status', [App\Http\Controllers\LaundryProvider\OrderController::class, 'updateStatus'])->name('update-status');

        // Single order routes - PUT THIS LAST
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\OrderController::class, 'show'])->name('show');
        Route::get('/{id}/print', [App\Http\Controllers\LaundryProvider\OrderController::class, 'printInvoice'])->name('print');

        // Bulk operations
        Route::post('/bulk-status', [App\Http\Controllers\LaundryProvider\OrderController::class, 'bulkUpdateStatus'])->name('bulk-status');
        Route::post('/bulk-assign', [App\Http\Controllers\LaundryProvider\OrderController::class, 'bulkAssign'])->name('bulk-assign');

        // Timeline/Calendar view
        Route::get('/calendar', [App\Http\Controllers\LaundryProvider\OrderController::class, 'calendar'])->name('calendar');
        Route::get('/timeline', [App\Http\Controllers\LaundryProvider\OrderController::class, 'timeline'])->name('timeline');
    });
    
    // ======= ITEMS & PRICING MANAGEMENT =======
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\ItemController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\LaundryProvider\ItemController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\LaundryProvider\ItemController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\ItemController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\LaundryProvider\ItemController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\LaundryProvider\ItemController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\LaundryProvider\ItemController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\LaundryProvider\ItemController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/duplicate', [App\Http\Controllers\LaundryProvider\ItemController::class, 'duplicate'])->name('duplicate');
        Route::post('/bulk-delete', [App\Http\Controllers\LaundryProvider\ItemController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-status', [App\Http\Controllers\LaundryProvider\ItemController::class, 'bulkUpdateStatus'])->name('bulk-status');
        Route::post('/bulk-price-update', [App\Http\Controllers\LaundryProvider\ItemController::class, 'bulkPriceUpdate'])->name('bulk-price-update');
        Route::get('/categories', [App\Http\Controllers\LaundryProvider\ItemController::class, 'categories'])->name('categories');
        Route::post('/categories', [App\Http\Controllers\LaundryProvider\ItemController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [App\Http\Controllers\LaundryProvider\ItemController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [App\Http\Controllers\LaundryProvider\ItemController::class, 'deleteCategory'])->name('categories.delete');
        Route::get('/export', [App\Http\Controllers\LaundryProvider\ItemController::class, 'export'])->name('export');
        Route::post('/import', [App\Http\Controllers\LaundryProvider\ItemController::class, 'import'])->name('import');
        Route::get('/template', [App\Http\Controllers\LaundryProvider\ItemController::class, 'downloadTemplate'])->name('template');
    });
    
    // ======= REVIEWS & RATINGS =======
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'pending'])->name('pending');
        Route::get('/responded', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'responded'])->name('responded');
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'reply'])->name('reply');
        Route::put('/{id}/reply', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'updateReply'])->name('update-reply');
        Route::delete('/{id}/reply', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'deleteReply'])->name('delete-reply');
        Route::get('/analytics/summary', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'summary'])->name('analytics.summary');
        Route::get('/analytics/ratings', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'ratingsBreakdown'])->name('analytics.ratings');
        Route::get('/analytics/export', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'exportReviews'])->name('analytics.export');
        Route::post('/{id}/report', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'report'])->name('report');
        Route::post('/{id}/helpful', [App\Http\Controllers\LaundryProvider\ReviewController::class, 'markHelpful'])->name('helpful');
    });
    
    // ======= PROFILE MANAGEMENT =======
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'update'])->name('update');
        Route::get('/business', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'businessInfo'])->name('business');
        Route::put('/business/update', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'updateBusiness'])->name('business.update');
        Route::get('/address', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'address'])->name('address');
        Route::post('/address', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'addAddress'])->name('address.add');
        Route::put('/address/{id}', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'updateAddress'])->name('address.update');
        Route::delete('/address/{id}', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'deleteAddress'])->name('address.delete');
        Route::post('/address/{id}/default', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'setDefaultAddress'])->name('address.default');
        Route::get('/address/{id}', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'getAddress'])->name('address.get');
        Route::post('/business-location', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'updateBusinessLocation'])->name('business.location');
        Route::get('/password', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'passwordForm'])->name('password');
        Route::put('/password/update', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'updatePassword'])->name('password.update');
        Route::post('/avatar', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::delete('/avatar', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::post('/geocode', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'geocode'])->name('geocode');
        Route::post('/reverse-geocode', [App\Http\Controllers\LaundryProvider\ProfileController::class, 'reverseGeocode'])->name('reverse-geocode');
    });
    
    // ======= NOTIFICATIONS =======
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'unread'])->name('unread');
        Route::get('/all', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'all'])->name('all');
        Route::get('/{id}', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'show'])->name('show');
        Route::post('/{id}/read', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'clearAll'])->name('clear-all');
        Route::get('/settings', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'updateSettings'])->name('update-settings');
        Route::get('/count/unread', [App\Http\Controllers\LaundryProvider\NotificationController::class, 'unreadCount'])->name('count.unread');
    });
    
    // ======= API/JSON ROUTES FOR AJAX =======
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/stats/today', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'todayStats'])->name('stats.today');
        Route::get('/stats/weekly', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'weeklyStats'])->name('stats.weekly');
        Route::get('/orders/recent', [App\Http\Controllers\LaundryProvider\OrderController::class, 'recentOrders'])->name('orders.recent');
        Route::get('/orders/upcoming', [App\Http\Controllers\LaundryProvider\OrderController::class, 'upcomingPickups'])->name('orders.upcoming');
        Route::get('/orders/overdue', [App\Http\Controllers\LaundryProvider\OrderController::class, 'overdueOrders'])->name('orders.overdue');
        Route::get('/items/popular', [App\Http\Controllers\LaundryProvider\ItemController::class, 'popularItems'])->name('items.popular');
        Route::get('/charts/orders', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'ordersChart'])->name('charts.orders');
        Route::get('/charts/earnings', [App\Http\Controllers\LaundryProvider\DashboardController::class, 'earningsChart'])->name('charts.earnings');
    });
});

// ============ SUPERADMIN ROUTES ============
Route::middleware(['auth', 'role:SUPERADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('role-applications')->name('role-applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\RoleApplicationController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Admin\RoleApplicationController::class, 'export'])->name('export');
        Route::get('/statistics', [App\Http\Controllers\Admin\RoleApplicationController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [App\Http\Controllers\Admin\RoleApplicationController::class, 'show'])->name('show');
        Route::get('/{id}/review', [App\Http\Controllers\Admin\RoleApplicationController::class, 'review'])->name('review');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\RoleApplicationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\RoleApplicationController::class, 'reject'])->name('reject');
        Route::get('/{id}/download-document', [App\Http\Controllers\Admin\RoleApplicationController::class, 'downloadDocument'])->name('download-document');
        Route::post('/bulk-approve', [App\Http\Controllers\Admin\RoleApplicationController::class, 'bulkApprove'])->name('bulk-approve');
    });
    
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('index');
        Route::post('/update', [App\Http\Controllers\Admin\CommissionController::class, 'update'])->name('update');
        Route::get('/rate/{type}', [App\Http\Controllers\Admin\CommissionController::class, 'getRate'])->name('rate');
        Route::post('/calculate', [App\Http\Controllers\Admin\CommissionController::class, 'calculate'])->name('calculate');
        Route::post('/reset', [App\Http\Controllers\Admin\CommissionController::class, 'reset'])->name('reset');
    });
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('export');
        Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
        Route::post('/{id}/status', [App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('assign-role');
        Route::post('/{id}/remove-role', [App\Http\Controllers\Admin\UserController::class, 'removeRole'])->name('remove-role');
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });
});

// ============ FALLBACK ROUTE ============
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->methods(['get']);