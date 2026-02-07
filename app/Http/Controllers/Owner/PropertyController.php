<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyAmenity;
use App\Models\PropertyImage;
use App\Models\CommissionConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::where('owner_id', Auth::id())
            ->withCount('rooms')
            ->latest()
            ->paginate(10);

        $stats = Property::getOwnerStats(Auth::id());

        return view('owner.properties.index', compact('properties', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $commissionRates = CommissionConfig::whereIn('service_type', ['HOSTEL', 'APARTMENT'])
            ->pluck('rate', 'service_type')
            ->toArray();
        
        // Ensure defaults
        $commissionRates['HOSTEL'] = $commissionRates['HOSTEL'] ?? 5.00;
        $commissionRates['APARTMENT'] = $commissionRates['APARTMENT'] ?? 3.00;
        
        return view('owner.properties.create', compact('commissionRates')); // FIXED
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePropertyRequest($request);
        
        DB::beginTransaction();
        
        try {
            // Create property
            $property = $this->createProperty($validated);
            
            // Handle cover image
            if ($request->hasFile('cover_image')) {
                $this->storeCoverImage($property, $request->file('cover_image'));
            }
            
            // Handle additional images
            if ($request->hasFile('additional_images')) {
                $this->storeAdditionalImages($property, $request->file('additional_images'));
            }
            
            // Handle rooms for hostel
            if ($property->type === 'HOSTEL' && isset($validated['rooms'])) {
                $this->createRooms($property, $validated['rooms']);
            }
            
            // Handle amenities
            if (isset($validated['amenities'])) {
                $this->createAmenities($property, $validated['amenities']);
            }
            
            DB::commit();
            
            return redirect()->route('owner.properties.index')
                ->with('success', 'Property created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files on error
            if (isset($property)) {
                Storage::disk('public')->deleteDirectory('properties/' . $property->id);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Check authorization
        if ($property->owner_id !== Auth::id() && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized action.');
        }
        
        $property->load(['rooms', 'amenities', 'images']);
        
        return view('owner.properties.show', compact('property')); // FIXED
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        // Check authorization
        if ($property->owner_id !== Auth::id() && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized action.');
        }
        
        $commissionRates = CommissionConfig::whereIn('service_type', ['HOSTEL', 'APARTMENT'])
            ->pluck('rate', 'service_type')
            ->toArray();
    
        // Ensure defaults
        $commissionRates['HOSTEL'] = $commissionRates['HOSTEL'] ?? 5.00;
        $commissionRates['APARTMENT'] = $commissionRates['APARTMENT'] ?? 3.00;
        
        // Load relationships
        $property->load(['rooms', 'amenities', 'images']);
        
        return view('owner.properties.edit', compact('property', 'commissionRates')); // FIXED
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        // Check authorization
        if ($property->owner_id !== Auth::id() && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $this->validatePropertyRequest($request, $property->id);
        
        DB::beginTransaction();
        
        try {
            // Update property
            $this->updateProperty($property, $validated);
            
            // Handle cover image update
            if ($request->hasFile('cover_image')) {
                // Delete old cover image
                if ($property->cover_image) {
                    Storage::disk('public')->delete($property->cover_image);
                }
                $this->storeCoverImage($property, $request->file('cover_image'));
            }
            
            // Handle additional images
            if ($request->hasFile('additional_images')) {
                $this->storeAdditionalImages($property, $request->file('additional_images'));
            }
            
            // Handle delete images
            if ($request->has('delete_images')) {
                $this->deleteImages($property, $request->input('delete_images'));
            }
            
            // Update rooms for hostel
            if ($property->type === 'HOSTEL') {
                $this->updateRooms($property, $validated['rooms'] ?? []);
            }
            
            // Update amenities
            $this->updateAmenities($property, $validated['amenities'] ?? []);
            
            DB::commit();
            
            return redirect()->route('owner.properties.show', $property) // FIXED
                ->with('success', 'Property updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to update property: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // Check authorization
        if ($property->owner_id !== Auth::id() && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete property images from storage
            if ($property->cover_image) {
                Storage::disk('public')->delete($property->cover_image);
            }
            
            // Delete additional images
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            // Delete property directory
            Storage::disk('public')->deleteDirectory('properties/' . $property->id);
            
            // Delete property (cascade will delete rooms, amenities, images)
            $property->delete();
            
            DB::commit();
            
            return redirect()->route('owner.properties.index')
                ->with('success', 'Property deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    /**
     * Update property status
     */
    public function updateStatus(Request $request, Property $property)
    {
        if ($property->owner_id !== Auth::id() && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'status' => 'required|in:DRAFT,PENDING,ACTIVE,INACTIVE'
        ]);
        
        $property->update(['status' => $request->status]);
        
        return back()->with('success', 'Property status updated successfully!');
    }

    /**
     * Validate property request
     */
    private function validatePropertyRequest(Request $request, $propertyId = null)
    {
        $rules = [
            'type' => 'required|in:HOSTEL,APARTMENT',
            'name' => 'required|string|max:150',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'gender_policy' => 'required|in:MALE_ONLY,FEMALE_ONLY,MIXED',
            'unit_size' => 'nullable|integer|min:1',
            'furnishing_status' => 'nullable|in:FURNISHED,SEMI_FURNISHED,UNFURNISHED',
            'base_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'min_stay_months' => 'nullable|integer|min:1',
            'deposit_months' => 'nullable|integer|min:0|max:12',
            'status' => 'required|in:DRAFT,PENDING,ACTIVE,INACTIVE',
            'cover_image' => 'nullable|image|max:10240|mimes:jpeg,png,jpg,gif',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'image|max:10240|mimes:jpeg,png,jpg,gif',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:property_images,id',
            'amenities' => 'nullable|array',
        ];
        
        // Add room validation rules for hostels
        if ($request->input('type') === 'HOSTEL') {
            $rules['rooms'] = 'required|array|min:1';
            $rules['rooms.*.id'] = 'nullable|exists:rooms,id';
            $rules['rooms.*.room_number'] = 'required|string|max:50';
            $rules['rooms.*.room_type'] = 'required|in:SINGLE,DOUBLE,TRIPLE,QUAD,DORM';
            $rules['rooms.*.floor_number'] = 'nullable|integer|min:0';
            $rules['rooms.*.capacity'] = 'required|integer|min:1';
            $rules['rooms.*.base_price'] = 'required|numeric|min:0';
            $rules['rooms.*.status'] = 'required|in:AVAILABLE,OCCUPIED,MAINTENANCE,RESERVED';
            $rules['rooms.*._destroy'] = 'nullable|boolean';
        }
        
        // Make cover image required for new properties
        if (!$propertyId) {
            $rules['cover_image'] = 'required|image|max:10240|mimes:jpeg,png,jpg,gif';
        }
        
        return $request->validate($rules);
    }

    /**
     * Create property record
     */
    private function createProperty(array $validated)
    {
        // Get commission rate from config or use default
        $commissionRate = $validated['commission_rate'] ?? 
            ($validated['type'] === 'HOSTEL' ? 5.00 : 3.00);
        
        $propertyData = [
            'owner_id' => Auth::id(),
            'type' => $validated['type'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'area' => $validated['area'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'gender_policy' => $validated['gender_policy'],
            'unit_size' => $validated['type'] === 'APARTMENT' ? ($validated['unit_size'] ?? null) : null,
            'furnishing_status' => $validated['furnishing_status'] ?? null,
            'base_price' => $validated['base_price'],
            'commission_rate' => $commissionRate,
            'deposit_months' => $validated['deposit_months'] ?? 1,
            'status' => $validated['status'],
        ];
        
        // Only set min_stay_months for apartments
        if ($validated['type'] === 'APARTMENT') {
            $propertyData['min_stay_months'] = $validated['min_stay_months'] ?? 3;
        } else {
            $propertyData['min_stay_months'] = 1; // Default for hostels
        }
        
        return Property::create($propertyData);
    }

    /**
     * Update property record
     */
    private function updateProperty(Property $property, array $validated)
    {
        // Get commission rate from config or use default
        $commissionRate = $validated['commission_rate'] ?? 
            ($validated['type'] === 'HOSTEL' ? 5.00 : 3.00);
        
        $updateData = [
            'type' => $validated['type'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'area' => $validated['area'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'gender_policy' => $validated['gender_policy'],
            'furnishing_status' => $validated['furnishing_status'] ?? null,
            'base_price' => $validated['base_price'],
            'commission_rate' => $commissionRate,
            'deposit_months' => $validated['deposit_months'] ?? 1,
            'status' => $validated['status'],
        ];
        
        // Only update unit_size and min_stay_months for apartments
        if ($validated['type'] === 'APARTMENT') {
            $updateData['unit_size'] = $validated['unit_size'] ?? null;
            $updateData['min_stay_months'] = $validated['min_stay_months'] ?? 3;
        } else {
            $updateData['unit_size'] = null;
            $updateData['min_stay_months'] = 1;
        }
        
        $property->update($updateData);
        
        return $property;
    }

    /**
     * Store cover image
     */
    private function storeCoverImage(Property $property, $image)
    {
        $path = $image->store("properties/{$property->id}/cover", 'public');
        
        PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => $path,
            'is_primary' => true,
            'display_order' => 1,
        ]);
    }

    /**
     * Store additional images
     */
    private function storeAdditionalImages(Property $property, array $images)
    {
        $order = $property->images()->max('display_order') ?? 1;
        
        foreach ($images as $image) {
            $order++;
            $path = $image->store("properties/{$property->id}/gallery", 'public');
            
            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $path,
                'is_primary' => false,
                'display_order' => $order,
            ]);
        }
    }

    /**
     * Delete images
     */
    private function deleteImages(Property $property, array $imageIds)
    {
        $images = $property->images()->whereIn('id', $imageIds)->get();
        
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        // If we deleted the primary image, make another one primary
        if ($property->images()->where('is_primary', true)->count() === 0) {
            $newPrimary = $property->images()->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }
    }

    /**
     * Create rooms for hostel
     */
    private function createRooms(Property $property, array $roomsData)
    {
        foreach ($roomsData as $roomData) {
            Room::create([
                'property_id' => $property->id,
                'room_number' => $roomData['room_number'],
                'room_type' => $roomData['room_type'],
                'floor_number' => $roomData['floor_number'] ?? null,
                'capacity' => $roomData['capacity'],
                'base_price' => $roomData['base_price'],
                'commission_rate' => $property->commission_rate,
                'status' => $roomData['status'],
            ]);
        }
    }

    /**
     * Update rooms for hostel
     */
    private function updateRooms(Property $property, array $roomsData)
    {
        $existingRoomIds = $property->rooms()->pluck('id')->toArray();
        $updatedRoomIds = [];
        
        foreach ($roomsData as $roomData) {
            if (isset($roomData['id'])) {
                // Update existing room
                $room = Room::where('id', $roomData['id'])
                    ->where('property_id', $property->id)
                    ->first();
                    
                if ($room && !isset($roomData['_destroy'])) {
                    $room->update([
                        'room_number' => $roomData['room_number'],
                        'room_type' => $roomData['room_type'],
                        'floor_number' => $roomData['floor_number'] ?? null,
                        'capacity' => $roomData['capacity'],
                        'base_price' => $roomData['base_price'],
                        'commission_rate' => $property->commission_rate,
                        'status' => $roomData['status'],
                    ]);
                    $updatedRoomIds[] = $room->id;
                } elseif ($room && isset($roomData['_destroy'])) {
                    // Mark for deletion
                    $room->delete();
                }
            } else {
                // Create new room
                $room = Room::create([
                    'property_id' => $property->id,
                    'room_number' => $roomData['room_number'],
                    'room_type' => $roomData['room_type'],
                    'floor_number' => $roomData['floor_number'] ?? null,
                    'capacity' => $roomData['capacity'],
                    'base_price' => $roomData['base_price'],
                    'commission_rate' => $property->commission_rate,
                    'status' => $roomData['status'],
                ]);
                $updatedRoomIds[] = $room->id;
            }
        }
        
        // Delete rooms not in updated list
        $roomsToDelete = array_diff($existingRoomIds, $updatedRoomIds);
        if (!empty($roomsToDelete)) {
            Room::whereIn('id', $roomsToDelete)->delete();
        }
    }

    /**
     * Create amenities
     */
    private function createAmenities(Property $property, array $amenitiesData)
    {
        foreach ($amenitiesData as $amenityName) {
            if (is_string($amenityName) && !empty($amenityName)) {
                PropertyAmenity::create([
                    'property_id' => $property->id,
                    'amenity_type' => 'BASIC', // Default type
                    'name' => $amenityName,
                ]);
            }
        }
    }

    /**
     * Update amenities
     */
    private function updateAmenities(Property $property, array $amenitiesData)
    {
        // Delete all existing amenities
        $property->amenities()->delete();
        
        // Create new amenities
        if (!empty($amenitiesData)) {
            $this->createAmenities($property, $amenitiesData);
        }
    }

    /**
     * Get commission rate API endpoint
     */
    public function getCommissionRate($type)
    {
        if (!in_array($type, ['HOSTEL', 'APARTMENT'])) {
            return response()->json(['error' => 'Invalid property type'], 400);
        }
        
        $commission = CommissionConfig::where('service_type', $type)->first();
        
        return response()->json([
            'rate' => $commission ? $commission->rate : ($type === 'HOSTEL' ? 5.00 : 3.00),
            'type' => $type
        ]);
    }
}