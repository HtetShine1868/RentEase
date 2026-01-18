<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class EmailVerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Create welcome notification
        $user = $request->user();
        $user->notifications()->create([
            'type' => 'SYSTEM',
            'title' => 'Welcome to RMS!',
            'message' => 'Thank you for verifying your email. You can now explore properties and services.',
            'channel' => 'IN_APP',
        ]);

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}