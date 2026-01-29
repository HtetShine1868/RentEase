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
        // Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // Check if email is verified
        if (!$user->isVerified()) {
            // Send verification code
            $user->sendVerificationCode();
            
            // Redirect to verification page
            return redirect()->route('verify.show')
                ->with('info', 'Please verify your email address to continue.');
        }

        // Already verified - redirect to appropriate dashboard
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

        // Check user roles and redirect accordingly
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {

            return redirect()->route('owner.dashboard');

        } elseif ($user->isFoodProvider()) {
            return redirect()->route('food.dashboard');
        } elseif ($user->isLaundryProvider()) {
            return redirect()->route('laundry.dashboard');
        } else {
            return redirect()->route('dashboard.user');
        }
    }
}