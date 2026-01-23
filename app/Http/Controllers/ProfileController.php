<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();
        $defaultAddress = $user->defaultAddress;
        
        return view('profile.show', [
            'user' => $user,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'title' => 'My Profile'
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $defaultAddress = $user->defaultAddress;
        
        return view('profile.edit', [
            'user' => $user,
            'defaultAddress' => $defaultAddress,
            'title' => 'Edit Profile'
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 
                        Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:MALE,FEMALE,OTHER'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_url'] = $avatarPath;
        }

        // Update user information
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'avatar_url' => $validated['avatar_url'] ?? $user->avatar_url,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the form for editing the user's address.
     */
    public function editAddress()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();
        $defaultAddress = $user->defaultAddress;
        
        return view('profile.edit-address', [
            'user' => $user,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'title' => 'Edit Address'
        ]);
    }

    /**
     * Update or create the user's address.
     */
    public function updateAddress(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'address_type' => ['required', 'in:HOME,WORK,OTHER'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['boolean'],
        ]);

        // If this is to be default, unset other defaults
        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        // Check if user has any address
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        // Create or update address
        $addressData = array_merge($validated, ['user_id' => $user->id]);
        
        // If editing existing default address
        if ($user->defaultAddress && $request->has('address_id')) {
            $address = $user->addresses()->findOrFail($request->address_id);
            $address->update($addressData);
        } else {
            UserAddress::create($addressData);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Show the form for editing the user's password.
     */
    public function editPassword()
    {
        return view('profile.edit-password', [
            'title' => 'Change Password'
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Delete a user address.
     */
    public function deleteAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        
        // Prevent deletion if it's the only address
        if ($user->addresses()->count() <= 1) {
            return back()->withErrors(['error' => 'Cannot delete your only address.']);
        }
        
        // If deleting default address, make another address default
        if ($address->is_default) {
            $newDefault = $user->addresses()->where('id', '!=', $id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }
        
        $address->delete();
        
        return redirect()->route('profile.show')
            ->with('success', 'Address deleted successfully!');
    }

    /**
     * Set an address as default.
     */
    public function setDefaultAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        
        // Unset all defaults
        $user->addresses()->update(['is_default' => false]);
        
        // Set this as default
        $address->update(['is_default' => true]);
        
        return redirect()->route('profile.show')
            ->with('success', 'Default address updated!');
    }
}