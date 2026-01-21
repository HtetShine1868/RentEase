<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on primary role
        if ($user->isSuperAdmin()) {
            return view('dashboard.admin', [
                'title' => 'SuperAdmin Dashboard'
            ]);
        } elseif ($user->isOwner()) {
            return view('dashboard.owner', [
                'title' => 'Property Owner Dashboard'
            ]);
        } elseif ($user->isFoodProvider()) {
            return view('dashboard.food', [
                'title' => 'Food Provider Dashboard'
            ]);
        } elseif ($user->isLaundryProvider()) {
            return view('dashboard.laundry', [
                'title' => 'Laundry Provider Dashboard'
            ]);
        }

        // Regular user dashboard
        return view('dashboard.user', [
            'title' => 'User Dashboard'
        ]);
    }
}