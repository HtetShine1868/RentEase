<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyImage;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Get an owner user
        $owner = User::where('email', 'owner@rms.com')->first();
        
        if (!$owner) {
            $owner = User::factory()->create([
                'name' => 'Property Owner',
                'email' => 'owner@rms.com',
                'password' => bcrypt('password'),
            ]);
        }
        
        // Sample Hostel
        $hostel = Property::create([
            'owner_id' => $owner->id,
            'type' => 'HOSTEL',
            'name' => 'Green Valley Hostel',
            'description' => 'A comfortable and secure hostel with excellent facilities for students and working professionals.',
            'address' => 'House 12, Road 8, Block D',
            'city' => 'Dhaka',
            'area' => 'Dhanmondi',
            'latitude' => 23.7465,
            'longitude' => 90.3760,
            'status' => 'ACTIVE',
            'gender_policy' => 'MIXED',
            'base_price' => 8000,
            'commission_rate' => 5.00,
        ]);
        
        // Add amenities for hostel
        $hostelAmenities = ['WiFi', 'AC Rooms', 'Security', 'Common Kitchen', 'Laundry Service', 'Study Room'];
        foreach ($hostelAmenities as $amenity) {
            PropertyAmenity::create([
                'property_id' => $hostel->id,
                'amenity_type' => 'BASIC',
                'name' => $amenity,
            ]);
        }
        
        // Add rooms for hostel
        $rooms = [
            ['room_number' => '101', 'room_type' => 'SINGLE', 'capacity' => 1, 'base_price' => 8000],
            ['room_number' => '102', 'room_type' => 'DOUBLE', 'capacity' => 2, 'base_price' => 12000],
            ['room_number' => '103', 'room_type' => 'TRIPLE', 'capacity' => 3, 'base_price' => 15000],
            ['room_number' => '201', 'room_type' => 'SINGLE', 'capacity' => 1, 'base_price' => 8000],
            ['room_number' => '202', 'room_type' => 'DOUBLE', 'capacity' => 2, 'base_price' => 12000],
        ];
        
        foreach ($rooms as $roomData) {
            Room::create(array_merge($roomData, [
                'property_id' => $hostel->id,
                'commission_rate' => 5.00,
                'status' => 'AVAILABLE',
            ]));
        }
        
        // Sample Apartment
        $apartment = Property::create([
            'owner_id' => $owner->id,
            'type' => 'APARTMENT',
            'name' => 'Lake View Apartments',
            'description' => 'Modern 2-bedroom apartment with beautiful lake view and all modern amenities.',
            'address' => 'Road 27, House 15',
            'city' => 'Dhaka',
            'area' => 'Gulshan',
            'latitude' => 23.7800,
            'longitude' => 90.4162,
            'status' => 'ACTIVE',
            'gender_policy' => 'MIXED',
            'unit_size' => 1200,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'furnishing_status' => 'FURNISHED',
            'min_stay_months' => 6,
            'deposit_months' => 2,
            'base_price' => 35000,
            'commission_rate' => 3.00,
        ]);
        
        // Add amenities for apartment
        $apartmentAmenities = ['WiFi', 'AC', 'Furnished', 'Parking', 'Security', 'Elevator', 'Generator'];
        foreach ($apartmentAmenities as $amenity) {
            PropertyAmenity::create([
                'property_id' => $apartment->id,
                'amenity_type' => 'BASIC',
                'name' => $amenity,
            ]);
        }
        
        $this->command->info('Sample properties created successfully!');
    }
}