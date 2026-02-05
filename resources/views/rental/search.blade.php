@extends('dashboard')

@section('title', 'Search Rental Properties')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900">Find Your Perfect Place</h1>
        <p class="mt-2 text-gray-600">Browse hostels and apartments with transparent pricing.</p>
    </div>

    <!-- Search and Filters Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Search Bar -->
        <form method="GET" action="{{ route('rental.search') }}" class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
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
        <div x-data="{ 
            showFilters: false,
            filters: {
                type: '{{ request('type', '') }}',
                min_price: '{{ request('min_price', '') }}',
                max_price: '{{ request('max_price', '') }}',
                city: '{{ request('city', '') }}',
                area: '{{ request('area', '') }}',
                gender_policy: '{{ request('gender_policy', '') }}',
                sort: '{{ request('sort', 'latest') }}'
            }
        }">
            <!-- Filter Toggle for Mobile -->
            <div class="md:hidden mb-4">
                <button @click="showFilters = !showFilters" 
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-lg">
                    <span class="font-medium text-gray-700">Filters</span>
                    <i class="fas fa-filter"></i>
                </button>
            </div>

            <!-- Filters Form -->
            <form method="GET" action="{{ route('rental.search') }}" 
                  class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4"
                  :class="showFilters ? 'block' : 'hidden md:grid'">
                
                <!-- Property Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                    <select name="type" x-model="filters.type"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Types</option>
                        <option value="HOSTEL" {{ request('type') == 'HOSTEL' ? 'selected' : '' }}>Hostel</option>
                        <option value="APARTMENT" {{ request('type') == 'APARTMENT' ? 'selected' : '' }}>Apartment</option>
                    </select>
                </div>

                <!-- Min Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Price (৳)</label>
                    <input type="number" name="min_price" x-model="filters.min_price"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="0" min="0" value="{{ request('min_price') }}">
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Price (৳)</label>
                    <input type="number" name="max_price" x-model="filters.max_price"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Any" min="0" value="{{ request('max_price') }}">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <select name="city" x-model="filters.city"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Policy -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender Policy</label>
                    <select name="gender_policy" x-model="filters.gender_policy"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any</option>
                        <option value="MALE_ONLY" {{ request('gender_policy') == 'MALE_ONLY' ? 'selected' : '' }}>Male Only</option>
                        <option value="FEMALE_ONLY" {{ request('gender_policy') == 'FEMALE_ONLY' ? 'selected' : '' }}>Female Only</option>
                        <option value="MIXED" {{ request('gender_policy') == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" x-model="filters.sort"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="lg:col-span-6 flex justify-between items-center pt-4 border-t border-gray-200">
                    <button type="button" @click="filters = {
                        type: '', min_price: '', max_price: '', city: '', 
                        gender_policy: '', sort: 'latest'
                    }" 
                            class="text-sm text-gray-600 hover:text-gray-900">
                        Clear All Filters
                    </button>
                    <div class="space-x-2">
                        <a href="{{ route('rental.search') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
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
                    <div class="flex flex-wrap gap-2">
                        @if(request('type'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                Type: {{ request('type') == 'HOSTEL' ? 'Hostel' : 'Apartment' }}
                                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="ml-1 text-blue-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('city'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                City: {{ request('city') }}
                                <a href="{{ request()->fullUrlWithQuery(['city' => null]) }}" class="ml-1 text-green-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                        @if(request('gender_policy'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                {{ ucfirst(strtolower(str_replace('_', ' ', request('gender_policy')))) }}
                                <a href="{{ request()->fullUrlWithQuery(['gender_policy' => null]) }}" class="ml-1 text-purple-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
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
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-home text-gray-300 text-4xl"></i>
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
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <span class="truncate">{{ $property->area }}, {{ $property->city }}</span>
                                </div>
                                
                                <!-- Property Features -->
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    @if($property->type === 'APARTMENT')
                                        <span>
                                            <i class="fas fa-expand-arrows-alt mr-1"></i>
                                            {{ $property->unit_size ? $property->unit_size . ' sqft' : 'N/A' }}
                                        </span>
                                    @endif
                                    <span>
                                        <i class="fas fa-bed mr-1"></i>
                                        {{ $property->bedrooms }} beds
                                    </span>
                                    <span>
                                        <i class="fas fa-bath mr-1"></i>
                                        {{ $property->bathrooms }} baths
                                    </span>
                                </div>
                                
                                <!-- Rating -->
                                @php
                                    $avgRating = $property->reviews->avg('overall_rating');
                                    $totalReviews = $property->reviews->count();
                                @endphp
                                
                                @if($avgRating)
                                    <div class="flex items-center mb-3">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($avgRating))
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $avgRating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">
                                            {{ number_format($avgRating, 1) }} 
                                            ({{ $totalReviews }} review{{ $totalReviews != 1 ? 's' : '' }})
                                        </span>
                                    </div>
                                @endif

                                <!-- Facilities -->
                                @if($property->amenities->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-4">
                                        @foreach($property->amenities->take(3) as $amenity)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                                <i class="fas fa-check mr-1 text-xs"></i>
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
                                    <a href="{{ route('rental.property.details', $property) }}" 
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium text-sm">
                                        View Details
                                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                    
                                    @if($property->type === 'APARTMENT')
                                        <a href="{{ route('rental.apartment.rent', $property) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            Rent Now
                                        </a>
                                    @else
                                        @php
                                            $availableRooms = $property->rooms->where('status', 'AVAILABLE')->count();
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
                    <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        @if(request()->anyFilled(['search', 'type', 'min_price', 'max_price', 'city', 'gender_policy']))
                            Try adjusting your filters to find what you're looking for.
                        @else
                            No properties are currently available. Check back soon!
                        @endif
                    </p>
                    @if(request()->anyFilled(['search', 'type', 'min_price', 'max_price', 'city', 'gender_policy']))
                        <a href="{{ route('rental.search') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Clear all filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Alpine.js for filters -->
<script src="//unpkg.com/alpinejs" defer></script>
@endsection