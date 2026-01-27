<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalSearchController extends Controller
{
    /**
     * Display the rental search page.
     */
    public function index(Request $request)
    {
        $query = Property::query()->active();
        
        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        // Filter by property type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->input('max_price'));
        }
        
        // Filter by location
        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }
        if ($request->filled('area')) {
            $query->where('area', 'like', "%{$request->input('area')}%");
        }
        
        // Filter by gender policy
        if ($request->filled('gender_policy')) {
            $query->where('gender_policy', $request->input('gender_policy'));
        }
        
        // Filter by rating
        if ($request->filled('min_rating')) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->havingRaw('AVG(overall_rating) >= ?', [$request->input('min_rating')]);
            });
        }
        
        // Sort results
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'rating':
                $query->orderByDesc(DB::raw('(SELECT AVG(overall_rating) FROM property_ratings WHERE property_id = properties.id)'));
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $properties = $query->with(['primaryImage', 'reviews'])
                          ->paginate(12)
                          ->withQueryString();
        
        // Get unique cities and areas for filters
        $cities = Property::active()->distinct()->pluck('city');
        $areas = Property::active()->distinct()->pluck('area');
        
        return view('rental.search', [
            'title' => 'Search Rental Properties',
            'properties' => $properties,
            'cities' => $cities,
            'areas' => $areas,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Display property details.
     */
    public function show(Property $property)
    {
        if ($property->status !== 'ACTIVE') {
            abort(404);
        }
        
        $property->load(['images', 'amenities', 'rooms' => function($query) {
            $query->where('status', 'AVAILABLE');
        }, 'reviews.user']);
        
        $relatedProperties = Property::active()
            ->where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->with('primaryImage')
            ->limit(4)
            ->get();
        
        return view('rental.property-details', [
            'title' => $property->name,
            'property' => $property,
            'relatedProperties' => $relatedProperties,
        ]);
    }

    /**
     * Display room details.
     */
    public function showRoom(Property $property, Room $room)
    {
        if ($property->status !== 'ACTIVE' || $room->status !== 'AVAILABLE') {
            abort(404);
        }
        
        $room->load('property');
        
        return view('rental.room-details', [
            'title' => $room->room_type_name . ' - ' . $property->name,
            'property' => $property,
            'room' => $room,
        ]);
    }

    /**
     * Show apartment rental form.
     */
    public function rentApartment(Property $property)
    {
        if ($property->type !== 'APARTMENT' || $property->status !== 'ACTIVE') {
            abort(404);
        }
        
        return view('rental.rent-apartment', [
            'title' => 'Rent ' . $property->name,
            'property' => $property,
        ]);
    }

    /**
     * Show hostel room rental form.
     */
    public function rentRoom(Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $property->status !== 'ACTIVE' || $room->status !== 'AVAILABLE') {
            abort(404);
        }
        
        return view('rental.rent-room', [
            'title' => 'Rent ' . $room->room_type_name . ' - ' . $property->name,
            'property' => $property,
            'room' => $room,
        ]);
    }
}