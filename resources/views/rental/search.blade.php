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
                min_rating: '{{ request('min_rating', '') }}',
                sort: '{{ request('sort', 'latest') }}'
            }
        }">
            <!-- Filter Toggle for Mobile -->
            <div class="md:hidden mb-4">
                <button @click="showFilters = !showFilters" 
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
                        <option value="HOSTEL">Hostel</option>
                        <option value="APARTMENT">Apartment</option>
                    </select>
                </div>

                <!-- Min Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Price (৳)</label>
                    <input type="number" name="min_price" x-model="filters.min_price"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="0" min="0">
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Price (৳)</label>
                    <input type="number" name="max_price" x-model="filters.max_price"
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Any" min="0">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <select name="city" x-model="filters.city"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Policy -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender Policy</label>
                    <select name="gender_policy" x-model="filters.gender_policy"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any</option>
                        <option value="MALE_ONLY">Male Only</option>
                        <option value="FEMALE_ONLY">Female Only</option>
                        <option value="MIXED">Mixed</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" x-model="filters.sort"
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="latest">Latest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                </div>

                <!-- Hidden inputs for other filters -->
                @if(request('area'))
                    <input type="hidden" name="area" value="{{ request('area') }}">
                @endif
                @if(request('min_rating'))
                    <input type="hidden" name="min_rating" value="{{ request('min_rating') }}">
                @endif

                <!-- Filter Actions -->
                <div class="lg:col-span-6 flex justify-between items-center pt-4 border-t border-gray-200">
                    <button type="button" @click="filters = {
                        type: '', min_price: '', max_price: '', city: '', 
                        area: '', gender_policy: '', min_rating: '', sort: 'latest'
                    }" 
                            class="text-sm text-gray-600 hover:text-gray-900">
                        Clear All Filters
                    </button>
                    <div class="space-x-2">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                <div class="flex space-x-2">
                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded-lg">
                        <button class="p-2 rounded-l-lg hover:bg-gray-100">
                            <i class="fas fa-th-large text-gray-600"></i>
                        </button>
                        <button class="p-2 rounded-r-lg hover:bg-gray-100 border-l border-gray-300">
                            <i class="fas fa-list text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties Grid -->
        <div class="p-6">
            @if($properties->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($properties as $property)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Property Image -->
                            <div class="relative h-48 bg-gray-100">
                                @if($property->primaryImage)
                                    <img src="{{ asset('storage/' . $property->primaryImage->image_path) }}" 
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
                                        {{ $property->type_name }}
                                    </span>
                                </div>
                                <!-- Price Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 bg-white rounded-lg shadow text-sm font-semibold">
                                        ৳{{ number_format($property->base_price) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Property Info -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ $property->name }}
                                </h4>
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $property->area }}, {{ $property->city }}
                                </div>
                                
                                <!-- Rating -->
                                @if($property->averageRating())
                                    <div class="flex items-center mb-3">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($property->averageRating()))
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $property->averageRating())
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">
                                            {{ number_format($property->averageRating(), 1) }} 
                                            ({{ $property->totalReviews() }} reviews)
                                        </span>
                                    </div>
                                @endif

                                <!-- Facilities -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($property->amenities->take(3) as $amenity)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                            <i class="fas fa-check mr-1"></i>
                                            {{ $amenity->name }}
                                        </span>
                                    @endforeach
                                    @if($property->amenities->count() > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                            +{{ $property->amenities->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <a href="{{ route('rental.property.details', $property) }}" 
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                                        View Details
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                    
                                    @if($property->type === 'APARTMENT')
                                        <a href="{{ route('rental.apartment.rent', $property) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            Rent Now
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-500">
                                            {{ $property->availableRooms->count() }} rooms available
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
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                    <a href="{{ route('rental.search') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection