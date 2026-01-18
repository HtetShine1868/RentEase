<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user is banned
        if ($user->status === 'BANNED') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been banned.']);
        }

        // Redirect based on primary role
        switch ($user->primaryRole) {
            case 'SUPERADMIN':
                return redirect()->route('admin.dashboard');
            case 'OWNER':
                return redirect()->route('owner.dashboard');
            case 'FOOD':
                return redirect()->route('food.dashboard');
            case 'LAUNDRY':
                return redirect()->route('laundry.dashboard');
            default:
                return redirect()->route('user.dashboard');
        }
    }
}