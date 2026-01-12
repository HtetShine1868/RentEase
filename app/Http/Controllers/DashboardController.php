<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('ADMIN')) {
            return redirect('/admin/dashboard');
        }

        if ($user->hasRole('OWNER')) {
            return redirect('/owner/dashboard');
        }

        if ($user->hasRole('FOOD')) {
            return redirect('/food/dashboard');
        }

        if ($user->hasRole('LAUNDRY')) {
            return redirect('/laundry/dashboard');
        }

        return view('user.dashboard');
    }
}
