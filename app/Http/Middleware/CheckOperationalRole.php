<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOperationalRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has the specific operational role
        if ($user->hasRole($role)) {
            return $next($request);
        }

        // If user doesn't have operational role, check if they can apply
        if (!$user->hasOperationalRole()) {
            session()->flash('info', 'You need to apply for ' . strtolower($role) . ' role to access this section.');
            return redirect()->route('applications.create', ['role' => strtolower($role)]);
        }

        // User has a different operational role
        session()->flash('error', 'You are registered as ' . $user->operational_role->name . '. Cannot access ' . $role . ' section.');
        return redirect()->route('dashboard');
    }
}