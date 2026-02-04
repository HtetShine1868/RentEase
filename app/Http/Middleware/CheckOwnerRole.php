<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOwnerRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!$user->hasRole('OWNER') && !$user->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized access. OWNER role required.');
        }
        
        return $next($request);
    }
}