<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $provider;
    protected $user;
    
    /**
     * Get the authenticated user and provider
     */
    private function getUserData()
    {
        $this->user = Auth::user();
        $this->provider = ServiceProvider::where('user_id', $this->user->id)
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        return [$this->user, $this->provider];
    }
    
    /**
     * Display the profile page
     */
    public function index()
    {
        list($user, $provider) = $this->getUserData();
        
        // Get user addresses
        try {
            $addresses = UserAddress::where('user_id', $user->id)->get();
        } catch (\Exception $e) {
            $addresses = collect([]);
            Log::error('Error loading addresses: ' . $e->getMessage());
        }
        
        return view('laundry-provider.profile.index', compact('user', 'provider', 'addresses'));
    }
    
    /**
     * Show edit profile form
     */
    public function edit()
    {
        list($user, $provider) = $this->getUserData();
        
        return view('laundry-provider.profile.edit', compact('user', 'provider'));
    }
    
    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->save();
        
        return redirect()->route('laundry-provider.profile.index')
            ->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Show business information form
     */
    public function businessInfo()
    {
        list($user, $provider) = $this->getUserData();
        
        return view('laundry-provider.profile.business', compact('user', 'provider'));
    }
    
    /**
     * Update business information
     */
    public function updateBusiness(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'service_radius_km' => 'required|numeric|min:1|max:100',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $provider->business_name = $request->business_name;
        $provider->description = $request->description;
        $provider->contact_email = $request->contact_email;
        $provider->contact_phone = $request->contact_phone;
        $provider->service_radius_km = $request->service_radius_km;
        $provider->save();
        
        return redirect()->route('laundry-provider.profile.business')
            ->with('success', 'Business information updated successfully.');
    }
    
    /**
     * Show address form
     */
    public function address()
    {
        list($user, $provider) = $this->getUserData();
        
        // Get all addresses
        try {
            $addresses = UserAddress::where('user_id', $user->id)->get();
        } catch (\Exception $e) {
            $addresses = collect([]);
            Log::error('Error loading addresses: ' . $e->getMessage());
        }
        
        // Get business location from provider
        $businessLocation = [
            'address' => $provider->address ?? '',
            'latitude' => $provider->latitude ?? 23.8103, // Default to Bangladesh
            'longitude' => $provider->longitude ?? 90.4125,
            'city' => $provider->city ?? '',
        ];
        
        return view('laundry-provider.profile.address', compact('user', 'provider', 'addresses', 'businessLocation'));
    }
    
    /**
     * Add new address
     */
    public function addAddress(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'address_type' => 'required|in:HOME,WORK,OTHER',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'sometimes|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // If this is set as default, remove default from other addresses
            if ($request->has('is_default') && $request->is_default) {
                UserAddress::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            $address = new UserAddress();
            $address->user_id = $user->id;
            $address->address_type = $request->address_type;
            $address->address_line1 = $request->address_line1;
            $address->address_line2 = $request->address_line2;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->postal_code = $request->postal_code;
            $address->country = $request->country ?? 'Bangladesh';
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->is_default = $request->has('is_default') ? true : false;
            $address->save();
            
            // If this is the first address, make it default
            if (UserAddress::where('user_id', $user->id)->count() == 1) {
                $address->is_default = true;
                $address->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Address added successfully.',
                'address' => $address
            ]);
            
        } catch (\Exception $e) {
            Log::error('Address add error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update address
     */
    public function updateAddress(Request $request, $id)
    {
        list($user, $provider) = $this->getUserData();
        
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();
            
            $validator = Validator::make($request->all(), [
                'address_type' => 'required|in:HOME,WORK,OTHER',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'is_default' => 'sometimes|boolean'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // If this is set as default, remove default from other addresses
            if ($request->has('is_default') && $request->is_default) {
                UserAddress::where('user_id', $user->id)
                    ->where('id', '!=', $id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            $address->address_type = $request->address_type;
            $address->address_line1 = $request->address_line1;
            $address->address_line2 = $request->address_line2;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->postal_code = $request->postal_code;
            $address->country = $request->country ?? 'Bangladesh';
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->is_default = $request->has('is_default') ? true : false;
            $address->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'address' => $address
            ]);
            
        } catch (\Exception $e) {
            Log::error('Address update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        list($user, $provider) = $this->getUserData();
        
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();
            
            $wasDefault = $address->is_default;
            $address->delete();
            
            // If deleted address was default, make another address default
            if ($wasDefault) {
                $newDefault = UserAddress::where('user_id', $user->id)->first();
                if ($newDefault) {
                    $newDefault->is_default = true;
                    $newDefault->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Address delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Set default address
     */
    public function setDefaultAddress($id)
    {
        list($user, $provider) = $this->getUserData();
        
        try {
            // Remove default from all addresses
            UserAddress::where('user_id', $user->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
            
            // Set new default
            $address = UserAddress::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();
            $address->is_default = true;
            $address->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Default address updated successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Set default address error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error setting default address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update business location from map
     */
    public function updateBusinessLocation(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $provider->address = $request->address;
            $provider->city = $request->city;
            $provider->latitude = $request->latitude;
            $provider->longitude = $request->longitude;
            $provider->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Business location updated successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Business location update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating business location: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show password change form
     */
    public function passwordForm()
    {
        list($user, $provider) = $this->getUserData();
        
        return view('laundry-provider.profile.password', compact('user'));
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return redirect()->route('laundry-provider.profile.index')
            ->with('success', 'Password updated successfully.');
    }
    
    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        list($user, $provider) = $this->getUserData();
        
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Delete old avatar if exists
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully.',
                'avatar_url' => Storage::url($path)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading avatar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete avatar
     */
    public function deleteAvatar()
    {
        list($user, $provider) = $this->getUserData();
        
        try {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
                $user->avatar_url = null;
                $user->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Avatar delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting avatar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get address for map
     */
    public function getAddress($id)
    {
        list($user, $provider) = $this->getUserData();
        
        try {
            $address = UserAddress::where('user_id', $user->id)
                ->where('id', $id)
                ->first();
            
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'address' => $address
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get address error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Geocode address (convert address to coordinates)
     */
    public function geocode(Request $request)
    {
        $request->validate([
            'address' => 'required|string'
        ]);
        
        try {
            // Using OpenStreetMap Nominatim API (free, no key required)
            $address = urlencode($request->address);
            $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Laundry Provider App');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geocoding service error'
                ], 500);
            }
            
            $data = json_decode($response, true);
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
                'display_name' => $data[0]['display_name']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Geocode error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error geocoding address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reverse geocode (convert coordinates to address)
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);
        
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?lat={$request->lat}&lon={$request->lng}&format=json";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Laundry Provider App');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reverse geocoding service error'
                ], 500);
            }
            
            $data = json_decode($response, true);
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'address' => $data['display_name'],
                'city' => $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? '',
                'state' => $data['address']['state'] ?? '',
                'country' => $data['address']['country'] ?? '',
                'postal_code' => $data['address']['postcode'] ?? ''
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reverse geocode error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error reverse geocoding: ' . $e->getMessage()
            ], 500);
        }
    }
}