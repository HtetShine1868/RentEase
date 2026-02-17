<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
       public function index()
    {
        return view('user.dashboard');
    }
    // In app/Http/Controllers/UserController.php
public function saveLocation(Request $request)
{
    try {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        
        $user = Auth::user();
        
        // Create or update default address with location
        $address = $user->addresses()->where('is_default', true)->first();
        
        if ($address) {
            $address->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);
        } else {
            // Create a temporary address with location
            $address = $user->addresses()->create([
                'address_type' => 'HOME',
                'address_line1' => 'Current Location',
                'city' => 'Unknown',
                'state' => 'Unknown',
                'country' => 'Bangladesh',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => true
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error saving location: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to save location'
        ], 500);
    }
}

}
