<!-- Restaurants Tab Content -->
<div>
    <!-- Location Bar -->
    <div class="mb-6 bg-indigo-50 p-4 rounded-lg">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-100 rounded-full p-2">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Delivering to:</p>
                    <button @click="showLocationModal = true" 
                            class="text-left font-medium text-indigo-700 hover:text-indigo-900 flex items-center">
                        <span x-text="selectedLocation?.address || 'Select Location'" 
                              class="max-w-xs truncate"></span>
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Location Info -->
            <template x-if="selectedLocation">
                <div class="flex items-center space-x-4 text-sm">
                    <span class="text-gray-600">
                        <span class="font-medium" x-text="deliveryDistance ? deliveryDistance + ' km' : 'Calculating...'"></span>
                    </span>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-600">
                        Delivery: 
                        <span class="font-medium text-indigo-600" x-text="'৳' + (deliveryFee || 0)"></span>
                    </span>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-600">
                        Est: 
                        <span class="font-medium" x-text="estimatedDeliveryTime ? estimatedDeliveryTime + ' min' : '30-45 min'"></span>
                    </span>
                </div>
            </template>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-6 space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input type="text" 
                   x-model="searchQuery"
                   @input.debounce.500ms="loadRestaurants(1)"
                   placeholder="Search for restaurants or cuisines..."
                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <div class="absolute left-3 top-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Filter Chips -->
        <div class="flex flex-wrap gap-2">
            <!-- Meal Type Filter -->
            <select x-model="selectedMealType" 
                    @change="loadRestaurants(1)"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Meals</option>
                <template x-for="mealType in mealTypes" :key="mealType.id">
                    <option :value="mealType.id" x-text="mealType.name"></option>
                </template>
            </select>

            <!-- Sort By -->
            <select x-model="sortBy" 
                    @change="loadRestaurants(1)"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="recommended">Recommended</option>
                <option value="rating">Highest Rated</option>
                <option value="distance">Nearest First</option>
                <option value="delivery_time">Fastest Delivery</option>
                <option value="delivery_fee">Lowest Delivery Fee</option>
                <option value="total_orders">Most Popular</option>
            </select>

            <!-- Cuisine Type Filter -->
            <button @click="showCuisineFilter = !showCuisineFilter"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 flex items-center">
                <span>Cuisine</span>
                <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Open Now Toggle -->
            <button @click="openNow = !openNow; loadRestaurants(1)"
                    class="px-3 py-2 border rounded-lg text-sm flex items-center"
                    :class="openNow ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 hover:bg-gray-50'">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Open Now
            </button>
        </div>

        <!-- Cuisine Filter Dropdown -->
        <div x-show="showCuisineFilter" 
             x-cloak
             class="absolute z-10 mt-1 w-64 bg-white rounded-lg shadow-lg border border-gray-200 p-4">
            <h4 class="font-medium text-gray-900 mb-2">Select Cuisine</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                <label class="flex items-center">
                    <input type="checkbox" value="bangladeshi" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Bangladeshi</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="indian" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Indian</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="chinese" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Chinese</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="thai" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Thai</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="fast_food" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Fast Food</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="italian" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Italian</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="mexican" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Mexican</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="japanese" class="rounded text-indigo-600">
                    <span class="ml-2 text-sm">Japanese</span>
                </label>
            </div>
            <div class="mt-3 flex justify-end space-x-2">
                <button @click="showCuisineFilter = false" 
                        class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                <button @click="applyCuisineFilter(); showCuisineFilter = false" 
                        class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Apply</button>
            </div>
        </div>
    </div>

    <!-- Restaurants Grid -->
    <div x-show="!isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="restaurant in restaurants" :key="restaurant.id">
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition cursor-pointer restaurant-card"
                 @click="viewRestaurant(restaurant.id)">
                <!-- Restaurant Image -->
                <div class="relative h-48 bg-gray-200">
                    <img :src="restaurant.image || '/images/default-restaurant.jpg'" 
                         :alt="restaurant.business_name"
                         class="w-full h-full object-cover">
                    
                    <!-- Status Badges -->
                    <div class="absolute top-2 left-2 flex space-x-2">
                        <span x-show="!restaurant.is_open" 
                              class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                            Closed
                        </span>
                        <span x-show="restaurant.discount_percent && restaurant.discount_percent > 0" 
                              class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                            <span x-text="restaurant.discount_percent"></span>% OFF
                        </span>
                    </div>
                    
                    <!-- Delivery Time -->
                    <div class="absolute bottom-2 right-2 bg-white px-2 py-1 rounded-lg text-sm font-medium shadow">
                        <span x-text="restaurant.estimated_delivery_minutes || '30-45' + ' min'"></span>
                    </div>
                </div>

                <!-- Restaurant Info -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="restaurant.business_name"></h3>
                        <div class="flex items-center bg-green-100 px-2 py-1 rounded">
                            <span class="text-sm font-medium text-green-800" x-text="restaurant.rating?.toFixed(1) || '0.0'"></span>
                            <svg class="h-4 w-4 text-green-600 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2" x-text="restaurant.description || 'No description available'"></p>
                    
                    <!-- Cuisine Types -->
                    <div class="flex flex-wrap gap-1 mb-3">
                        <template x-if="restaurant.cuisine_types && restaurant.cuisine_types.length > 0">
                            <>
                                <template x-for="cuisine in restaurant.cuisine_types.slice(0, 3)" :key="cuisine">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full" 
                                          x-text="cuisine"></span>
                                </template>
                                <span x-show="restaurant.cuisine_types.length > 3" 
                                      class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    +<span x-text="restaurant.cuisine_types.length - 3"></span>
                                </span>
                            </>
                        </template>
                    </div>

                    <!-- Restaurant Details -->
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center text-gray-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <span x-text="restaurant.distance_km || 'N/A'"></span>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-text="restaurant.estimated_delivery_minutes ? restaurant.estimated_delivery_minutes + ' min' : '30-45 min'"></span>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-text="'৳' + (restaurant.delivery_fee || 0)"></span>
                        </div>
                    </div>

                    <!-- Popular Items -->
                    <div x-show="restaurant.popular_items && restaurant.popular_items.length > 0" 
                         class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-2">Popular:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="item in restaurant.popular_items.slice(0, 3)" :key="item.id">
                                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-full"
                                      x-text="item.name"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Rating Breakdown -->
                    <div x-show="restaurant.rating_breakdown" class="mt-3 flex items-center text-xs text-gray-500">
                        <span class="mr-3">Quality: <span class="font-medium" x-text="restaurant.rating_breakdown?.quality?.toFixed(1) || '0.0'"></span></span>
                        <span class="mr-3">Delivery: <span class="font-medium" x-text="restaurant.rating_breakdown?.delivery?.toFixed(1) || '0.0'"></span></span>
                        <span>Value: <span class="font-medium" x-text="restaurant.rating_breakdown?.value?.toFixed(1) || '0.0'"></span></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="isLoading" class="text-center py-12">
        <div class="loading-spinner"></div>
        <p class="mt-4 text-gray-600">Loading restaurants...</p>
    </div>

    <!-- No Results -->
    <div x-show="!isLoading && (!restaurants || restaurants.length === 0)" class="text-center py-12">
        <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No restaurants found</h3>
        <p class="text-gray-600 mb-4">Try adjusting your filters or search in a different area</p>
        <button @click="resetFilters()" 
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Clear Filters
        </button>
    </div>

    <!-- Load More -->
    <div x-show="currentPage < lastPage" class="text-center mt-8">
        <button @click="loadMore()" 
                :disabled="isLoading"
                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50">
            Load More Restaurants
        </button>
    </div>
</div>

<!-- Add this to your main CSS file or style section -->
<style>
.restaurant-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.restaurant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>