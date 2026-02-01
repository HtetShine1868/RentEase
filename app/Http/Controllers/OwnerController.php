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

    // ADD THIS METHOD BELOW
    public function bookings()
    {
        // 1. Fetch the bookings (usually for the logged-in owner)
        $bookings = Booking::where('user_id', auth()->id())->get();

        // 2. Fetch the properties (this is the missing piece causing your error!)
        $properties = Property::where('owner_id', auth()->id())->get();

        // 3. Pass both to the view
        return view('owner.pages.bookings.index', compact('bookings', 'properties'));
    }
}