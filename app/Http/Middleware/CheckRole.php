<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is banned
        if ($user->status === 'BANNED') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been banned. Please contact support.'
            ]);
        }

        // Check if user has the required role
        if (!$user->hasRole($role)) {
            // Redirect based on primary role
            $primaryRole = $user->primaryRole;
            
            return match($primaryRole) {
                'SUPERADMIN' => redirect()->route('admin.dashboard'),
                'OWNER' => redirect()->route('owner.dashboard'),
                'FOOD' => redirect()->route('food.dashboard'),
                'LAUNDRY' => redirect()->route('laundry.dashboard'),
                default => redirect()->route('user.dashboard'),
            };
        }

        return $next($request);
    }
}