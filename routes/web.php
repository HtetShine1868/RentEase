<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::middleware(['auth', 'verified', 'role:ADMIN'])->get('/admin/dashboard', fn () => view('admin.dashboard'));
Route::middleware(['auth', 'verified', 'role:OWNER'])->get('/owner/dashboard', fn () => view('owner.dashboard'));
Route::middleware(['auth', 'verified', 'role:FOOD'])->get('/food/dashboard', fn () => view('food.dashboard'));
Route::middleware(['auth', 'verified', 'role:LAUNDRY'])->get('/laundry/dashboard', fn () => view('laundry.dashboard'));

require __DIR__.'/auth.php';
