@extends('dashboard')

@section('title', 'Search Rental Properties')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900">Find Your Perfect Place</h1>
            <p class="mt-2 text-gray-600">Browse hostels and apartments with transparent pricing</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search & Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('properties.search') }}" class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="search" 
                           name="search"
                           value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Search by property name, city, area...">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Search
                    </button>
                </div>
            </form>

            <!-- Filters -->
            <form method="GET" action="{{ route('properties.search') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                
                <!-- Property Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Types</option>
                        <option value="HOSTEL" {{ request('type') == 'HOSTEL' ? 'selected' : '' }}>Hostel</option>
                        <option value="APARTMENT" {{ request('type') == 'APARTMENT' ? 'selected' : '' }}>Apartment</option>
                    </select>
                </div>

                <!-- Min Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="৳0">
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="৳Any">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <select name="city" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Policy -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender_policy" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any</option>
                        <option value="MALE_ONLY" {{ request('gender_policy') == 'MALE_ONLY' ? 'selected' : '' }}>Male Only</option>
                        <option value="FEMALE_ONLY" {{ request('gender_policy') == 'FEMALE_ONLY' ? 'selected' : '' }}>Female Only</option>
                        <option value="MIXED" {{ request('gender_policy') == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="lg:col-span-6 flex justify-between items-center pt-4 border-t border-gray-200">
                    @if(request()->anyFilled(['search', 'type', 'min_price', 'max_price', 'city', 'gender_policy']))
                        <a href="{{ route('properties.search') }}" 
                           class="text-sm text-gray-600 hover:text-gray-900">
                            Clear All Filters
                        </a>
                    @endif
                    <div class="space-x-2">
                        <a href="{{ route('properties.search') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Available Properties 
                    @if($properties->total())
                        <span class="text-sm text-gray-500 font-normal">
                            ({{ $properties->total() }} found)
                        </span>
                    @endif
                </h3>
                
                <!-- Active Filters -->
                @if(request()->anyFilled(['type', 'min_price', 'max_price', 'city', 'gender_policy']))
                    <div class="flex flex-wrap gap-2 mt-2">
                        @if(request('type'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                {{ request('type') == 'HOSTEL' ? 'Hostel' : 'Apartment' }}
                                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="ml-1 text-blue-600">
                                    &times;
                                </a>
                            </span>
                        @endif
                        @if(request('city'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                {{ request('city') }}
                                <a href="{{ request()->fullUrlWithQuery(['city' => null]) }}" class="ml-1 text-green-600">
                                    &times;
                                </a>
                            </span>
                        @endif
                        @if(request('min_price'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                Min: ৳{{ number_format(request('min_price')) }}
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null]) }}" class="ml-1 text-yellow-600">
                                    &times;
                                </a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Properties Grid -->
            <div class="p-6">
                @if($properties->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <!-- Property Image -->
                                <div class="relative h-48 bg-gray-100">
                                    @if($property->primaryImage)
                                        <img src="{{ Storage::url($property->primaryImage->image_path) }}" 
                                             alt="{{ $property->name }}"
                                             class="w-full h-full object-cover">
                                    @elseif($property->images && $property->images->count())
                                        <img src="{{ Storage::url($property->images->first()->image_path) }}" 
                                             alt="{{ $property->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Property Type Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $property->type === 'HOSTEL' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $property->type === 'HOSTEL' ? 'Hostel' : 'Apartment' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Price Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 bg-white rounded-lg shadow text-sm font-semibold">
                                            ৳{{ number_format($property->base_price) }}/month
                                        </span>
                                    </div>
                                    
                                    <!-- Gender Policy Badge -->
                                    <div class="absolute bottom-3 left-3">
                                        @php
                                            $genderColors = [
                                                'MALE_ONLY' => 'bg-blue-500',
                                                'FEMALE_ONLY' => 'bg-pink-500',
                                                'MIXED' => 'bg-purple-500'
                                            ];
                                            $genderText = [
                                                'MALE_ONLY' => 'Male Only',
                                                'FEMALE_ONLY' => 'Female Only',
                                                'MIXED' => 'Mixed'
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full text-white {{ $genderColors[$property->gender_policy] ?? 'bg-gray-500' }}">
                                            {{ $genderText[$property->gender_policy] ?? 'Mixed' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Property Info -->
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1 truncate">
                                        {{ $property->name }}
                                    </h4>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $property->area }}, {{ $property->city }}</span>
                                    </div>
                                    
                                    <!-- Property Features -->
                                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                        @if($property->type === 'APARTMENT')
                                            <span>
                                                <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                </svg>
                                                {{ $property->unit_size ? $property->unit_size . ' sqft' : 'N/A' }}
                                            </span>
                                        @endif
                                        <span>
                                            <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $property->bedrooms }} beds
                                        </span>
                                        <span>
                                            <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $property->bathrooms }} baths
                                        </span>
                                    </div>
                                    
                                    <!-- Rating -->
                                    @php
                                        $avgRating = $property->reviews_avg_overall_rating ?? 0;
                                        $totalReviews = $property->reviews_count ?? 0;
                                    @endphp
                                    
                                    @if($avgRating > 0)
                                        <div class="flex items-center mb-3">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($avgRating))
                                                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @elseif($i - 0.5 <= $avgRating)
                                                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <defs>
                                                                <linearGradient id="half-{{ $property->id }}-{{ $i }}">
                                                                    <stop offset="50%" stop-color="currentColor"/>
                                                                    <stop offset="50%" stop-color="#D1D5DB"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <path fill="url(#half-{{ $property->id }}-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">
                                                {{ number_format($avgRating, 1) }} 
                                                ({{ $totalReviews }} review{{ $totalReviews != 1 ? 's' : '' }})
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Amenities -->
                                    @if($property->amenities && $property->amenities->count() > 0)
                                        <div class="flex flex-wrap gap-1 mb-4">
                                            @foreach($property->amenities->take(3) as $amenity)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                                    <svg class="h-3 w-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ Str::limit($amenity->name, 15) }}
                                                </span>
                                            @endforeach
                                            @if($property->amenities->count() > 3)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                                    +{{ $property->amenities->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                        <a href="{{ route('properties.show', $property) }}" 
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium text-sm">
                                            View Details
                                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                        
                                        @if($property->type === 'APARTMENT')
                                            <a href="{{ route('properties.rent', $property) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                Rent Now
                                            </a>
                                        @else
                                            @php
                                                $availableRooms = $property->rooms_count ?? $property->rooms->where('status', 'AVAILABLE')->count();
                                            @endphp
                                            <span class="text-sm {{ $availableRooms > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $availableRooms }} room{{ $availableRooms != 1 ? 's' : '' }} available
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($properties->hasPages())
                        <div class="mt-8">
                            {{ $properties->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No properties found</h3>
                        <p class="mt-2 text-gray-500 max-w-md mx-auto">
                            @if(request()->anyFilled(['search', 'type', 'min_price', 'max_price', 'city', 'gender_policy']))
                                Try adjusting your filters to find what you're looking for.
                            @else
                                No properties are currently available. Check back soon!
                            @endif
                        </p>
                        @if(request()->anyFilled(['search', 'type', 'min_price', 'max_price', 'city', 'gender_policy']))
                            <a href="{{ route('properties.search') }}" 
                               class="mt-6 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Clear all filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection