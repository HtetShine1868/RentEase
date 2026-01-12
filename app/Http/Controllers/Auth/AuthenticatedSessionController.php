<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();

         $user = Auth::user();
        // Enforce email verification
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice');
        }

        return redirect()->route('dashboard');
}


    /**
     * Handle a successful authentication attempt.
     */
    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
