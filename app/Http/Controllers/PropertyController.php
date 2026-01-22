<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyAmenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $properties = Property::where('owner_id', $user->id)
            ->withCount(['rooms', 'bookings'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $stats = [
            'total' => Property::where('owner_id', $user->id)->count(),
            'active' => Property::where('owner_id', $user->id)->active()->count(),
            'draft' => Property::where('owner_id', $user->id)->draft()->count(),
            'inactive' => Property::where('owner_id', $user->id)->where('status', 'INACTIVE')->count(),
        ];
        
        return view('properties.index', [
            'title' => 'My Properties',
            'properties' => $properties,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('properties.create', [
            'title' => 'Add New Property',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate based on property type
        $rules = [
            'type' => ['required', 'in:HOSTEL,APARTMENT'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'gender_policy' => ['required', 'in:MALE_ONLY,FEMALE_ONLY,MIXED'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
        ];
        
        // Additional rules for apartments
        if ($request->type === 'APARTMENT') {
            $rules = array_merge($rules, [
                'unit_size' => ['required', 'integer', 'min:1'],
                'bedrooms' => ['required', 'integer', 'min:1'],
                'bathrooms' => ['required', 'integer', 'min:1'],
                'furnishing_status' => ['required', 'in:FURNISHED,SEMI_FURNISHED,UNFURNISHED'],
                'min_stay_months' => ['required', 'integer', 'min:1'],
                'deposit_months' => ['required', 'integer', 'min:0'],
            ]);
        }
        
        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        try {
            // Create property
            $property = Property::create([
                'owner_id' => $user->id,
                'type' => $validated['type'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'area' => $validated['area'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'status' => 'DRAFT',
                'gender_policy' => $validated['gender_policy'],
                'base_price' => $validated['base_price'],
                'commission_rate' => $validated['commission_rate'],
                'unit_size' => $validated['unit_size'] ?? null,
                'bedrooms' => $validated['bedrooms'] ?? null,
                'bathrooms' => $validated['bathrooms'] ?? null,
                'furnishing_status' => $validated['furnishing_status'] ?? null,
                'min_stay_months' => $validated['min_stay_months'] ?? 1,
                'deposit_months' => $validated['deposit_months'] ?? 1,
            ]);
            
            // Add amenities if provided
            if (!empty($validated['amenities'])) {
                foreach ($validated['amenities'] as $amenity) {
                    if (!empty(trim($amenity))) {
                        PropertyAmenity::create([
                            'property_id' => $property->id,
                            'amenity_type' => 'BASIC',
                            'name' => trim($amenity),
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('properties.show', $property->id)
                ->with('success', 'Property created successfully! You can now add rooms if this is a hostel.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create property. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $property->load(['rooms', 'amenities', 'bookings' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);
        
        return view('properties.show', [
            'title' => $property->name,
            'property' => $property,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $property->load('amenities');
        
        return view('properties.edit', [
            'title' => 'Edit Property: ' . $property->name,
            'property' => $property,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'gender_policy' => ['required', 'in:MALE_ONLY,FEMALE_ONLY,MIXED'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
        ];
        
        // Additional rules for apartments
        if ($property->type === 'APARTMENT') {
            $rules = array_merge($rules, [
                'unit_size' => ['required', 'integer', 'min:1'],
                'bedrooms' => ['required', 'integer', 'min:1'],
                'bathrooms' => ['required', 'integer', 'min:1'],
                'furnishing_status' => ['required', 'in:FURNISHED,SEMI_FURNISHED,UNFURNISHED'],
                'min_stay_months' => ['required', 'integer', 'min:1'],
                'deposit_months' => ['required', 'integer', 'min:0'],
            ]);
        }
        
        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        try {
            // Update property
            $property->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'area' => $validated['area'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'gender_policy' => $validated['gender_policy'],
                'base_price' => $validated['base_price'],
                'commission_rate' => $validated['commission_rate'],
                'unit_size' => $validated['unit_size'] ?? $property->unit_size,
                'bedrooms' => $validated['bedrooms'] ?? $property->bedrooms,
                'bathrooms' => $validated['bathrooms'] ?? $property->bathrooms,
                'furnishing_status' => $validated['furnishing_status'] ?? $property->furnishing_status,
                'min_stay_months' => $validated['min_stay_months'] ?? $property->min_stay_months,
                'deposit_months' => $validated['deposit_months'] ?? $property->deposit_months,
            ]);
            
            // Update amenities
            $property->amenities()->delete();
            if (!empty($validated['amenities'])) {
                foreach ($validated['amenities'] as $amenity) {
                    if (!empty(trim($amenity))) {
                        PropertyAmenity::create([
                            'property_id' => $property->id,
                            'amenity_type' => 'BASIC',
                            'name' => trim($amenity),
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('properties.show', $property->id)
                ->with('success', 'Property updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update property. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        // Only allow deletion of draft properties
        if ($property->status !== 'DRAFT') {
            return back()->withErrors(['error' => 'Only draft properties can be deleted.']);
        }
        
        DB::beginTransaction();
        try {
            // Delete related data
            $property->amenities()->delete();
            $property->rooms()->delete();
            $property->delete();
            
            DB::commit();
            
            return redirect()->route('properties.index')
                ->with('success', 'Property deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete property. Please try again.']);
        }
    }

    /**
     * Update property status.
     */
    public function updateStatus(Request $request, Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'status' => ['required', 'in:DRAFT,ACTIVE,INACTIVE'],
        ]);
        
        // Additional validation for activating property
        if ($request->status === 'ACTIVE') {
            // For hostels, check if there are rooms
            if ($property->type === 'HOSTEL' && !$property->rooms()->exists()) {
                return back()->withErrors(['error' => 'Cannot activate hostel without rooms. Please add rooms first.']);
            }
        }
        
        $property->update(['status' => $request->status]);
        
        $statusName = match($request->status) {
            'ACTIVE' => 'activated',
            'INACTIVE' => 'deactivated',
            'DRAFT' => 'moved to draft',
            default => 'updated',
        };
        
        return back()->with('success', "Property {$statusName} successfully!");
    }

    /**
     * Show property analytics.
     */
    public function analytics(Property $property)
    {
        // Check if user owns this property
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
        
        // Calculate earnings
        $totalEarnings = $property->bookings()
            ->where('status', 'CHECKED_OUT')
            ->sum('total_amount');
            
        $commissionPaid = $property->bookings()
            ->where('status', 'CHECKED_OUT')
            ->sum('commission_amount');
            
        $netEarnings = $totalEarnings - $commissionPaid;
        
        // Monthly earnings
        $monthlyEarnings = $property->bookings()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total, SUM(commission_amount) as commission')
            ->where('status', 'CHECKED_OUT')
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        return view('properties.analytics', [
            'title' => 'Property Analytics: ' . $property->name,
            'property' => $property,
            'totalEarnings' => $totalEarnings,
            'commissionPaid' => $commissionPaid,
            'netEarnings' => $netEarnings,
            'monthlyEarnings' => $monthlyEarnings,
        ]);
    }
}