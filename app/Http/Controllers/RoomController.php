<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Show the form for creating a new room.
     */
    public function create(Property $property)
    {
        // Check if user owns this property and it's a hostel
        if ($property->owner_id !== Auth::id() || $property->type !== 'HOSTEL') {
            abort(403);
        }
        
        return view('rooms.create', [
            'title' => 'Add Room to ' . $property->name,
            'property' => $property,
        ]);
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request, Property $property)
    {
        // Check if user owns this property and it's a hostel
        if ($property->owner_id !== Auth::id() || $property->type !== 'HOSTEL') {
            abort(403);
        }
        
        $request->validate([
            'room_number' => ['required', 'string', 'max:50'],
            'room_type' => ['required', 'in:SINGLE,DOUBLE,TRIPLE,QUAD,DORM'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        
        // Check if room number already exists for this property
        $existingRoom = Room::where('property_id', $property->id)
            ->where('room_number', $request->room_number)
            ->exists();
            
        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists for this property.']);
        }
        
        DB::beginTransaction();
        try {
            $room = Room::create([
                'property_id' => $property->id,
                'room_number' => $request->room_number,
                'room_type' => $request->room_type,
                'floor_number' => $request->floor_number,
                'capacity' => $request->capacity,
                'base_price' => $request->base_price,
                'commission_rate' => $request->commission_rate,
                'status' => 'AVAILABLE',
            ]);
            
            DB::commit();
            
            return redirect()->route('properties.show', $property->id)
                ->with('success', 'Room added successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add room. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Property $property, Room $room)
    {
        // Check if user owns this property and room belongs to property
        if ($property->owner_id !== Auth::id() || $room->property_id !== $property->id) {
            abort(403);
        }
        
        return view('rooms.edit', [
            'title' => 'Edit Room: ' . $room->room_number,
            'property' => $property,
            'room' => $room,
        ]);
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Property $property, Room $room)
    {
        // Check if user owns this property and room belongs to property
        if ($property->owner_id !== Auth::id() || $room->property_id !== $property->id) {
            abort(403);
        }
        
        $request->validate([
            'room_number' => ['required', 'string', 'max:50'],
            'room_type' => ['required', 'in:SINGLE,DOUBLE,TRIPLE,QUAD,DORM'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:AVAILABLE,OCCUPIED,MAINTENANCE,RESERVED'],
        ]);
        
        // Check if room number already exists for this property (excluding current room)
        $existingRoom = Room::where('property_id', $property->id)
            ->where('room_number', $request->room_number)
            ->where('id', '!=', $room->id)
            ->exists();
            
        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists for this property.']);
        }
        
        $room->update([
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'floor_number' => $request->floor_number,
            'capacity' => $request->capacity,
            'base_price' => $request->base_price,
            'commission_rate' => $request->commission_rate,
            'status' => $request->status,
        ]);
        
        return redirect()->route('properties.show', $property->id)
            ->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Property $property, Room $room)
    {
        // Check if user owns this property and room belongs to property
        if ($property->owner_id !== Auth::id() || $room->property_id !== $property->id) {
            abort(403);
        }
        
        // Check if room has active bookings
        $hasActiveBookings = $room->bookings()
            ->whereNotIn('status', ['CANCELLED', 'CHECKED_OUT'])
            ->exists();
            
        if ($hasActiveBookings) {
            return back()->withErrors(['error' => 'Cannot delete room with active bookings.']);
        }
        
        $room->delete();
        
        return redirect()->route('properties.show', $property->id)
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Update room status.
     */
    public function updateStatus(Request $request, Property $property, Room $room)
    {
        // Check if user owns this property and room belongs to property
        if ($property->owner_id !== Auth::id() || $room->property_id !== $property->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => ['required', 'in:AVAILABLE,OCCUPIED,MAINTENANCE,RESERVED'],
        ]);
        
        $room->update(['status' => $request->status]);
        
        return back()->with('success', 'Room status updated successfully!');
    }
}