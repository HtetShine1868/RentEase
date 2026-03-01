@extends('layouts.apps')

@section('title', $property->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('properties.search') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Search
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ Str::limit($property->name, 30) }}</span>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Property Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $property->name }}</h1>
                    <div class="flex items-center mt-2">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-600">{{ $property->address }}, {{ $property->area }}, {{ $property->city }}</span>
                    </div>
                    <div class="flex items-center mt-3 space-x-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            {{ $property->type === 'HOSTEL' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $property->type_name }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ $property->gender_policy_name }}
                        </span>
                        @if($property->furnishing_status)
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                {{ $property->furnishing_status_name }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6">
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900">MMK{{ number_format($property->total_price) }}/month</div>
                        <div class="text-sm text-gray-500 mt-1">Base: MMK{{ number_format($property->base_price) }} + {{ $property->commission_rate }}% commission</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Gallery -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            @if($property->images->count())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-1">
                    @foreach($property->images as $index => $image)
                        <div class="{{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}">
                            <img src="{{ Storage::url($image->image_path) }}" 
                                 alt="{{ $property->name }} - Image {{ $index + 1 }}"
                                 class="w-full h-full object-cover aspect-video">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-96 bg-gray-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-gray-500">No images available</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">Description</h3>
                    <div class="prose max-w-none text-gray-600">
                        <p class="whitespace-pre-line">{{ $property->description ?? 'No description available.' }}</p>
                    </div>
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">Property Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($property->type === 'APARTMENT')
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-500">Unit Size</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $property->unit_size ? number_format($property->unit_size) . ' sqft' : 'N/A' }}
                                </div>
                            </div>
                        @endif
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500">Bedrooms</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $property->bedrooms }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500">Bathrooms</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $property->bathrooms }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500">Min Stay</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $property->min_stay_months }} month(s)</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-500">Deposit</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $property->deposit_months }} month(s)</div>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                @if($property->amenities->count())
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-medium text-gray-900 mb-4">Facilities & Amenities</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($property->amenities as $amenity)
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Room Types (for Hostels) -->
                @if($property->type === 'HOSTEL' && $property->rooms->count())
                    <div class="bg-white rounded-lg shadow p-6" id="rooms">
                        <h3 class="text-xl font-medium text-gray-900 mb-4">Available Rooms</h3>
                        <div class="space-y-4">
                            @foreach($property->rooms->groupBy('room_type') as $roomType => $rooms)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">{{ $rooms->first()->room_type_name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-users mr-1"></i> Capacity: {{ $rooms->first()->capacity }} persons
                                            </p>
                                        </div>
                                        <div class="mt-2 md:mt-0 text-right">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                MMK{{ number_format($rooms->first()->total_price) }}/month
                                            </div>
                                            <div class="text-sm text-green-600">
                                                {{ $rooms->count() }} available
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($rooms as $room)
                                            <a href="{{ route('properties.rooms.book', [$property, $room]) }}" 
                                               class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                Request to Book Room {{ $room->room_number }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                @if($property->reviews->count())
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-medium text-gray-900 mb-4">
                            Reviews & Ratings
                            <span class="text-sm font-normal text-gray-500 ml-2">
                                (Average: {{ number_format($averageRating ?? 0, 1) }})
                            </span>
                        </h3>
                        <div class="space-y-4">
                            @foreach($property->reviews->take(5) as $review)
                                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center mb-2">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                            @if($review->user->avatar_url)
                                                <img src="{{ Storage::url($review->user->avatar_url) }}" 
                                                     alt="{{ $review->user->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-user-circle text-gray-500 text-2xl"></i>
                                            @endif
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
                                    @if($review->comment)
                                        <p class="text-gray-600 mt-2">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Booking Card -->
            <div class="space-y-6">
                <!-- Booking Card -->
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">
                        @if($property->type === 'APARTMENT')
                            Request to Rent
                        @else
                            Book a Room
                        @endif
                    </h3>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Price</span>
                            <span class="font-medium">MMK{{ number_format($property->base_price) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Commission ({{ $property->commission_rate }}%)</span>
                            <span class="font-medium">MMK{{ number_format($property->base_price * $property->commission_rate / 100) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total Price</span>
                                <span class="text-2xl font-bold text-indigo-600">MMK{{ number_format($property->total_price) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">per month</p>
                        </div>
                    </div>

                    <!-- How it works notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <div class="text-xs text-blue-700">
                                <strong>How it works:</strong> Submit a request to the owner. You'll be notified when approved, then you can make payment.
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    @if($property->type === 'APARTMENT')
                        <a href="{{ route('properties.rent', $property) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-3">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Rental Request
                        </a>
                    @elseif($property->rooms->count())
                        <div class="text-center mb-4">
                            <p class="text-gray-600 mb-3">Select a room type below to proceed</p>
                            <a href="#rooms" 
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-door-open mr-2"></i>
                                View Available Rooms
                            </a>
                        </div>
                    @endif

                    <!-- Property Owner Info with Chat -->
                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div class="flex items-center">
                            <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Property Owner</div>
                                <div class="font-medium">{{ $property->owner->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <!-- CHAT WITH OWNER BUTTON -->
                        @auth
                            @if(Auth::id() !== $property->owner_id)
                                <div class="mt-4">
                                    <a href="{{ route('rental.chat.start', ['property' => $property->id]) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-comment mr-2"></i>
                                        Chat with Owner
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="mt-4">
                                <a href="{{ route('login') }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login to Chat with Owner
                                </a>
                            </div>
                        @endauth

                        @if($property->owner->phone)
                            <div class="flex items-center mt-3">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Contact</div>
                                    <div class="font-medium">{{ $property->owner->phone }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Property Rules -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">Rules & Policies</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">Check-in: 2:00 PM | Check-out: 12:00 PM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lock text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">Quiet hours: 10:00 PM to 7:00 AM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-user-friends text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">No visitors after 9:00 PM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-ban text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">No smoking | No pets allowed</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        @if(isset($relatedProperties) && $relatedProperties->count())
            <div class="mt-6">
                <h3 class="text-xl font-medium text-gray-900 mb-6">Similar Properties</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProperties as $related)
                        <a href="{{ route('properties.show', $related) }}" 
                           class="group bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow">
                            <div class="h-40 bg-gray-100">
                                @if($related->primaryImage)
                                    <img src="{{ Storage::url($related->primaryImage->image_path) }}" 
                                         alt="{{ $related->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-building text-3xl text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 mb-1">
                                    {{ Str::limit($related->name, 25) }}
                                </h4>
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1 text-xs"></i>
                                    {{ $related->area }}
                                </div>
                                <div class="text-lg font-semibold text-gray-900">
                                    MMK{{ number_format($related->total_price) }}/month
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection