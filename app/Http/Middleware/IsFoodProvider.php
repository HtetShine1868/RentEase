<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsFoodProvider
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!$user->hasRole('FOOD')) {
            abort(403, 'Unauthorized access. Food provider role required.');
        }
        
        return $next($request);
    }
}