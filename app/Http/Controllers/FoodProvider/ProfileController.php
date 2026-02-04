<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MealType;
use App\Models\ServiceProvider;

class ProfileController extends Controller
{
    /**
     * Display the food provider's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load service provider with relationships
        $serviceProvider = $user->serviceProvider()->with(['foodServiceConfig', 'mealTypes'])->firstOrFail();
        
        $foodConfig = $serviceProvider->foodServiceConfig ?? null;
        $mealTypes = $serviceProvider->mealTypes ?? collect();

        return view('food-provider.profile.index', compact('serviceProvider', 'foodConfig', 'mealTypes'));
    }

    /**
     * Show the form for editing the food provider's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Load service provider with mealTypes relationship
        $serviceProvider = $user->serviceProvider()->with('mealTypes')->firstOrFail();
        
        $foodConfig = $serviceProvider->foodServiceConfig ?? null;
        $allMealTypes = MealType::all();
        
        // Handle null mealTypes gracefully
        $serviceProviderMealTypes = $serviceProvider->mealTypes 
            ? $serviceProvider->mealTypes->pluck('id')->toArray() 
            : [];

        return view('food-provider.profile.edit', compact(
            'serviceProvider', 
            'foodConfig', 
            'allMealTypes', 
            'serviceProviderMealTypes'
        ));
    }

    /**
     * Update the food provider's profile in storage.
     */
public function update(Request $request)
{
    \Log::info('=== PROFILE UPDATE STARTED ===');
    \Log::info('User ID: ' . Auth::id());
    \Log::info('All request data:', $request->all());
    
    $user = Auth::user();
    $serviceProvider = $user->serviceProvider;
    
    \Log::info('Current service provider data:', [
        'id' => $serviceProvider->id,
        'business_name' => $serviceProvider->business_name,
        'description' => $serviceProvider->description,
        'contact_phone' => $serviceProvider->contact_phone,
    ]);
    
    DB::beginTransaction();
    \Log::info('Transaction started');
    
    try {
        // 1. Update user avatar if provided
        if ($request->hasFile('logo')) {
            \Log::info('Logo file detected');
            $path = $request->file('logo')->store('avatars', 'public');
            $user->avatar_url = Storage::url($path);
            $user->save();
            \Log::info('User avatar updated');
        }
        
        // 2. Update service provider - DIRECT ASSIGNMENT
        \Log::info('Updating service provider with data:', [
            'business_name' => $request->business_name,
            'description' => $request->description,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'address' => $request->address,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'service_radius_km' => $request->service_radius_km,
        ]);
        
        // Use direct assignment instead of update()
        $serviceProvider->business_name = $request->business_name;
        $serviceProvider->description = $request->description;
        $serviceProvider->contact_phone = $request->contact_phone;
        $serviceProvider->contact_email = $request->contact_email;
        $serviceProvider->address = $request->address;
        $serviceProvider->city = $request->city;
        $serviceProvider->latitude = $request->latitude;
        $serviceProvider->longitude = $request->longitude;
        $serviceProvider->service_radius_km = $request->service_radius_km;
        
        $saved = $serviceProvider->save();
        \Log::info('Service provider save result: ' . ($saved ? 'SUCCESS' : 'FAILED'));
        \Log::info('Service provider changes: ', $serviceProvider->getChanges());
        \Log::info('Service provider dirty attributes: ', $serviceProvider->getDirty());
        
        // 3. Update or create food service config
        $foodConfigData = [
            'supports_subscription' => $request->has('supports_subscription') ? 1 : 0,
            'supports_pay_per_eat' => $request->has('supports_pay_per_eat') ? 1 : 0,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'avg_preparation_minutes' => $request->avg_preparation_minutes,
            'delivery_buffer_minutes' => $request->delivery_buffer_minutes,
        ];
        
        \Log::info('Food config data:', $foodConfigData);
        
        if ($serviceProvider->foodServiceConfig) {
            $foodConfigUpdated = $serviceProvider->foodServiceConfig()->update($foodConfigData);
            \Log::info('Food config update result: ' . ($foodConfigUpdated ? 'SUCCESS' : 'FAILED'));
        } else {
            $foodConfigData['service_provider_id'] = $serviceProvider->id;
            $foodConfigCreated = \DB::table('food_service_configs')->insert($foodConfigData);
            \Log::info('Food config create result: ' . ($foodConfigCreated ? 'SUCCESS' : 'FAILED'));
        }
        
        DB::commit();
        \Log::info('=== TRANSACTION COMMITTED ===');
        
        // Refresh and log updated data
        $serviceProvider->refresh();
        \Log::info('Updated service provider data:', [
            'business_name' => $serviceProvider->business_name,
            'description' => $serviceProvider->description,
            'contact_phone' => $serviceProvider->contact_phone,
        ]);
        
        return redirect()->route('food-provider.profile.index')
            ->with('success', 'Profile updated successfully!')
            ->with('debug_info', 'Update processed - check logs');
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Profile update failed: ' . $e->getMessage());
        \Log::error('Exception trace: ', $e->getTrace());
        
        return redirect()->back()
            ->with('error', 'Failed to update profile: ' . $e->getMessage())
            ->withInput();
    }
}
    public function handleProfile(Request $request)
{
    if ($request->isMethod('GET')) {
        // Show edit form
        return $this->edit();
    } else {
        // Process update
        return $this->update($request);
    }
}
}