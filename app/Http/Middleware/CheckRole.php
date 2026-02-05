<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        \Log::info("CheckRole middleware called with role parameter: {$role}");
        \Log::info("User roles: " . Auth::user()->roles->pluck('name')->implode(', '));

        if (!Auth::user()->hasRole($role)) {
            abort(403, "Unauthorized. Required role: {$role}");
        }

        return $next($request);
    }
}