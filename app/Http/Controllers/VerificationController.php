<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    // Show verification page
    public function show()
    {
        // Must be logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If already verified, go to dashboard
        if ($user->isVerified()) {
            return $this->redirectToDashboard();
        }

        return view('auth.verify', [
            'email' => $user->email
        ]);
    }

    // Verify the code
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verify code
        if ($user->verifyCode($request->code)) {
            return $this->redirectToDashboard()
                ->with('success', 'Email verified successfully!');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    // Resend code
    public function resend()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }
                if ($user->isVerified()) {
            return $this->redirectToDashboard();
        }

        $user->sendVerificationCode();

        return back()->with('success', 'New code sent! Check your email.');
    }

    // Redirect to appropriate dashboard based on role
    private function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        } elseif ($user->isFoodProvider()) {
            return redirect()->route('food.dashboard');
        } elseif ($user->isLaundryProvider()) {
            return redirect()->route('laundry.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
}