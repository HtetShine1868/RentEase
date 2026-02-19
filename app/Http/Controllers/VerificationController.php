<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    // Show verification page
    public function show()
    {
        // Check if user is logged in OR has email in session
        $email = session('verifying_user_email');
        
        if (!$email && !Auth::check()) {
            return redirect()->route('login');
        }

        // If logged in, use logged in user
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            // Otherwise get user from session email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Session expired. Please login.');
            }
        }

        // If already verified, go to dashboard
        if ($user->isVerified()) {
            // Clear session if exists
            session()->forget('verifying_user_email');
            
            // Login the user if not already logged in
            if (!Auth::check()) {
                Auth::login($user);
            }
            
            return $this->redirectToDashboard();
        }

        return view('auth.verify', [
            'email' => $user->email,
            'can_resend' => $user->canRequestNewCode(),
            'attempts' => $user->verification_attempts,
        ]);
    }

    // Verify the code
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        // Find user either from session or auth
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $email = session('verifying_user_email');
            if (!$email) {
                return redirect()->route('login')->with('error', 'Session expired.');
            }
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found.');
            }
        }

        // Verify code
        if ($user->verifyCode($request->code)) {
            \Log::info('Verification successful', ['user_id' => $user->id]);
            
            // Clear session
            session()->forget('verifying_user_email');
            
            // Login the user if not already logged in
            if (!Auth::check()) {
                Auth::login($user);
            }
            
                return redirect()->route('dashboard')
                ->with('success', 'Email verified successfully!');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    // Resend code
    public function resend()
    {
        // Find user
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $email = session('verifying_user_email');
            if (!$email) {
                return redirect()->route('login');
            }
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('login');
            }
        }

        if ($user->isVerified()) {
            return $this->redirectToDashboard();
        }

        // Check if can resend
        if (!$user->canRequestNewCode()) {
            return back()->with('error', 'Please wait before requesting new code.');
        }

        $user->sendVerificationCode();

        return back()->with('success', 'New code sent! Check your email.');
    }

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