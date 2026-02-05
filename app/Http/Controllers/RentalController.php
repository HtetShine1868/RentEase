<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function search(Request $request)
    {
        $query = Property::query()->active()->with(['primaryImage', 'amenities', 'reviews']);
        
        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }
        
        // Filter by property type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        
        // Filter by area
        if ($request->filled('area')) {
            $query->where('area', 'like', "%{$request->area}%");
        }
        
        // Filter by gender policy
        if ($request->filled('gender_policy')) {
            $query->where('gender_policy', $request->gender_policy);
        }
        
        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->select('property_id')
                  ->groupBy('property_id')
                  ->havingRaw('AVG(overall_rating) >= ?', [$request->min_rating]);
            });
        }
        
        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'overall_rating')
                      ->orderByDesc('reviews_avg_overall_rating');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }
        
        // Get distinct cities for filter dropdown
        $cities = Property::active()->distinct()->pluck('city')->sort();
        
        $properties = $query->paginate(12)->withQueryString();
        
        return view('rental.search', compact('properties', 'cities'));
    }
    
    public function show(Property $property)
    {
        $property->load([
            'images' => function($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('display_order');
            },
            'amenities',
            'reviews.user',
            'rooms' => function($q) {
                $q->where('status', 'AVAILABLE');
            }
        ]);
        
        // Related properties (same city, different property)
        $relatedProperties = Property::where('city', $property->city)
            ->where('id', '!=', $property->id)
            ->active()
            ->with('primaryImage')
            ->limit(3)
            ->get();
        
        return view('rental.property-details', compact('property', 'relatedProperties'));
    }
    
    public function rentApartment(Property $property)
    {
        if ($property->type !== 'APARTMENT') {
            abort(404);
        }
        
        $property->load(['images', 'amenities']);
        
        return view('rental.apartment-rent', compact('property'));
    }
    
    public function bookRoom(Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL') {
            abort(404);
        }
        
        $property->load(['images', 'amenities']);
        
        return view('rental.room-booking', compact('property', 'room'));
    }
}