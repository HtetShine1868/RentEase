<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Try to login
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        $user = Auth::user();

        // Check if email is verified
        if (!$user->isVerified()) {
            // Send verification code
            $user->sendVerificationCode();
            // Go to verification page
           return redirect()->route('verify.show');
        }

        // Already verified - go to appropriate dashboard
        return $this->redirectToDashboard();
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard.admin');
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.pages.dashboard');
        } elseif ($user->isFoodProvider()) {
            return redirect()->route('food-provider.dashboard.index');
        } elseif ($user->isLaundryProvider()) {
            return redirect()->route('dashboard.laundry');
        } else {
            return redirect()->route('dashboard.user');
        }
    }
}