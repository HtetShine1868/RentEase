<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;  // Make sure to import your models!
use App\Models\Property; 

class OwnerController extends Controller
{
    public function index()
    {
        return view('owner.dashboard');
    }

}