<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is verified
        if (!$user->isVerified()) {
            return redirect()->route('verify.show');
        }
        
        // Redirect based on role
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        } elseif ($user->isFoodProvider()) {
            return redirect()->route('food-provider.dashboard');
        } elseif ($user->isLaundryProvider()) {
            return redirect()->route('laundry.dashboard');
        } else {
            // Regular user - go to user dashboard
            return redirect()->route('dashboard.user');
        }
    }
}