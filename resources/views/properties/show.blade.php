@extends('owner.layout.owner-layout')

@section('title', $property->name . ' - RentEase')
@section('page-title', 'Property Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $property->name }}</h1>
                <div class="flex items-center gap-4 mt-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $property->status_badge }}">
                        {{ $property->status }}
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-{{ $property->type === 'HOSTEL' ? 'bed' : 'building' }} mr-1"></i>
                        {{ $property->type_name }}
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $property->city }}, {{ $property->area }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('owner.properties.edit', $property) }}" 
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('owner.properties.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-medium">{{ $property->type_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Gender Policy:</span>
                    <span class="font-medium">{{ $property->gender_policy_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Bedrooms:</span>
                    <span class="font-medium">{{ $property->bedrooms }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Bathrooms:</span>
                    <span class="font-medium">{{ $property->bathrooms }}</span>
                </div>
                @if($property->unit_size)
                <div class="flex justify-between">
                    <span class="text-gray-600">Unit Size:</span>
                    <span class="font-medium">{{ $property->unit_size }} sqft</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Furnishing:</span>
                    <span class="font-medium">{{ $property->furnishing_status_name }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Base Price:</span>
                    <span class="font-medium">৳{{ number_format($property->base_price, 2) }}/month</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Commission Rate:</span>
                    <span class="font-medium">{{ $property->commission_rate }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Price:</span>
                    <span class="font-medium text-green-600">৳{{ number_format($property->total_price, 2) }}/month</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Security Deposit:</span>
                    <span class="font-medium">{{ $property->deposit_months }} month(s)</span>
                </div>
                @if($property->min_stay_months > 1)
                <div class="flex justify-between">
                    <span class="text-gray-600">Minimum Stay:</span>
                    <span class="font-medium">{{ $property->min_stay_months }} months</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
        <p class="text-gray-700 whitespace-pre-line">{{ $property->description }}</p>
    </div>

    <!-- Location -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
        <div class="space-y-2">
            <p class="text-gray-700">{{ $property->address }}</p>
            <p class="text-gray-600">{{ $property->area }}, {{ $property->city }}</p>
            <p class="text-sm text-gray-500">
                Coordinates: {{ number_format($property->latitude, 6) }}, {{ number_format($property->longitude, 6) }}
            </p>
        </div>
    </div>

    <!-- Rooms (for hostels) -->
    @if($property->type === 'HOSTEL' && $property->rooms->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rooms ({{ $property->rooms->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($property->rooms as $room)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $room->room_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $room->room_type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $room->floor_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $room->capacity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">৳{{ number_format($room->base_price, 2) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'AVAILABLE' => 'bg-green-100 text-green-800',
                                    'OCCUPIED' => 'bg-red-100 text-red-800',
                                    'MAINTENANCE' => 'bg-yellow-100 text-yellow-800',
                                    'RESERVED' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$room->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $room->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Amenities -->
    @if($property->amenities->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Amenities ({{ $property->amenities->count() }})</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($property->amenities as $amenity)
            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                {{ $amenity->name }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Images -->
    @if($property->images->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Images ({{ $property->images->count() }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($property->images as $image)
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $image->image_path) }}" 
                     alt="Property Image" 
                     class="w-full h-48 object-cover">
                @if($image->is_primary)
                <div class="p-2 bg-blue-50 text-blue-700 text-sm text-center">
                    <i class="fas fa-star mr-1"></i> Cover Image
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection