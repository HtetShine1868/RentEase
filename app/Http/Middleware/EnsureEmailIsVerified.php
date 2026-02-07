<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if email is verified
        if (!$user->isVerified()) {
            // If user is trying to access verification routes, allow it
            if ($request->routeIs('verification.*')) {
                return $next($request);
            }
            
            // Store intended URL for after verification
            if ($request->isMethod('GET')) {
                Session::put('url.intended', $request->fullUrl());
            }
            
            // Send verification code if not sent recently
            if ($user->canRequestNewCode()) {
                $user->sendVerificationCode();
            }
            
            return redirect()->route('verification.show')
                ->with('info', 'Please verify your email address to continue.');
        }

        return $next($request);
    }
}