@extends('dashboard')

@section('title', $property->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('rental.search') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-search mr-2"></i>
                    Search Rental
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $property->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Property Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $property->name }}</h1>
                <div class="flex items-center mt-2">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                    <span class="text-gray-600">{{ $property->address }}, {{ $property->area }}, {{ $property->city }}</span>
                </div>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        {{ $property->type === 'HOSTEL' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $property->type_name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $property->gender_policy_name }}
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">৳{{ number_format($property->total_price) }}/month</div>
                    <div class="text-sm text-gray-500">Base price: ৳{{ number_format($property->base_price) }} + {{ $property->commission_rate }}% commission</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Gallery -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-1">
            @if($property->images->count())
                @foreach($property->images->take(3) as $image)
                    <div class="{{ $loop->first ? 'md:col-span-2 md:row-span-2' : '' }}">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="{{ $property->name }}"
                             class="w-full h-full object-cover aspect-video">
                    </div>
                @endforeach
            @else
                <div class="md:col-span-3 h-96 bg-gray-100 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-home text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">No images available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                <div class="prose max-w-none text-gray-600">
                    {{ $property->description }}
                </div>
            </div>

            <!-- Amenities -->
            @if($property->amenities->count())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Facilities & Amenities</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($property->amenities as $amenity)
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Room Types (for Hostels) -->
            @if($property->type === 'HOSTEL' && $property->rooms->count())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Rooms</h3>
                    <div class="space-y-4">
                        @foreach($property->rooms->groupBy('room_type') as $roomType => $rooms)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $rooms->first()->room_type_name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-users mr-1"></i>
                                            Capacity: {{ $rooms->first()->capacity }} persons
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900">
                                            ৳{{ number_format($rooms->first()->total_price) }}/month
                                        </div>
                                        <div class="text-sm text-green-600">
                                            {{ $rooms->count() }} available
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('rental.room.details', [$property, $rooms->first()]) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    View Room Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reviews -->
            @if($property->reviews->count())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Reviews & Ratings
                        <span class="text-sm font-normal text-gray-500">
                            (Average: {{ number_format($property->averageRating(), 1) }})
                        </span>
                    </h3>
                    <div class="space-y-4">
                        @foreach($property->reviews->take(3) as $review)
                            <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center mb-2">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->overall_rating)
                                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                @else
                                                    <i class="far fa-star text-gray-300 text-sm"></i>
                                                @endif
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-500">
                                                {{ $review->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-600">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Booking Card -->
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rent This Property</h3>
                
                <!-- Price Breakdown -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Price</span>
                        <span class="font-medium">৳{{ number_format($property->base_price) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Commission ({{ $property->commission_rate }}%)</span>
                        <span class="font-medium">৳{{ number_format($property->base_price * $property->commission_rate / 100) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-medium text-gray-900">Total Price</span>
                            <span class="text-2xl font-bold text-indigo-600">৳{{ number_format($property->total_price) }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">per month</p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                @if($property->type === 'APARTMENT')
                    <a href="{{ route('rental.apartment.rent', $property) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-3">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Rent Apartment
                    </a>
                @else
                    <div class="text-center mb-4">
                        <p class="text-gray-600 mb-3">Select a room type to proceed</p>
                        <a href="#rooms" 
                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-bed mr-2"></i>
                            View Available Rooms
                        </a>
                    </div>
                @endif

                <!-- Property Info -->
                <div class="space-y-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Property Owner</div>
                            <div class="font-medium">{{ $property->owner->name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Contact</div>
                            <div class="font-medium">{{ $property->owner->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rules & Policies -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rules & Policies</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Check-in: 2:00 PM | Check-out: 12:00 PM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-user-friends text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Max {{ $property->type === 'APARTMENT' ? 'occupants: ' . $property->bedrooms * 2 : 'room capacity as shown' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-ban text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">No smoking | No pets</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Properties -->
    @if($relatedProperties->count())
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Similar Properties</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProperties as $related)
                    <a href="{{ route('rental.property.details', $related) }}" 
                       class="group border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div class="h-40 bg-gray-100">
                            @if($related->primaryImage)
                                <img src="{{ asset('storage/' . $related->primaryImage->image_path) }}" 
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-home text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 mb-1">
                                {{ Str::limit($related->name, 25) }}
                            </h4>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $related->area }}
                            </div>
                            <div class="text-lg font-semibold text-gray-900">
                                ৳{{ number_format($related->total_price) }}/month
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection