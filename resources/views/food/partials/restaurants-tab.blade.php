<div>
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.500ms="loadRestaurants()"
                           placeholder="Search restaurants or dishes..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <select x-model="selectedMealType" @change="loadRestaurants()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 min-w-[150px]">
                    <option value="">All Meal Types</option>
                    @foreach($mealTypes as $mealType)
                    <option value="{{ $mealType->id }}">{{ $mealType->name }}</option>
                    @endforeach
                </select>
                <select x-model="sortBy" @change="loadRestaurants()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 min-w-[150px]">
                    <option value="rating">Rating (Highest)</option>
                    <option value="rating_low">Rating (Lowest)</option>
                    <option value="distance">Distance (Nearest)</option>
                    <option value="delivery_time">Delivery Time (Fastest)</option>
                    <option value="total_orders">Most Orders</option>
                    <option value="total_ratings">Most Reviewed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    <div x-show="searchQuery || selectedMealType" class="mb-4 flex flex-wrap gap-2">
        <template x-if="searchQuery">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800">
                <i class="fas fa-search mr-1"></i> <span x-text="'Search: ' + searchQuery"></span>
                <button @click="searchQuery = ''; loadRestaurants()" class="ml-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        </template>
        <template x-if="selectedMealType">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800">
                <i class="fas fa-utensils mr-1"></i> <span x-text="`Meal: ${getMealTypeName(selectedMealType)}`"></span>
                <button @click="selectedMealType = ''; loadRestaurants()" class="ml-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        </template>
    </div>

    <!-- Restaurant Grid - Only show dynamic restaurants -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Show loading skeleton while loading -->
        <template x-if="isLoading && restaurants.length === 0">
            <div class="col-span-full text-center py-12">
                <div class="loading-spinner"></div>
                <p class="mt-4 text-gray-600">Loading restaurants...</p>
            </div>
        </template>

        <!-- Dynamic restaurants from AJAX -->
        <template x-for="restaurant in restaurants" :key="restaurant.id">
            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow relative">
                <div class="h-48 bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center relative">
                    <img x-show="restaurant.image" :src="restaurant.image" class="w-full h-full object-cover">
                    <i x-show="!restaurant.image" class="fas fa-utensils text-indigo-300 text-5xl"></i>
                    
                    <span x-show="restaurant.in_service_area" 
                          class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>In Service Area
                    </span>
                    
                    <span x-show="restaurant.discount_percent > 0" 
                          class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        <i class="fas fa-tag mr-1"></i><span x-text="restaurant.discount_percent"></span>% OFF Subscription
                    </span>
                </div>
                
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900" x-text="restaurant.business_name"></h3>
                            
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <template x-for="i in 5" :key="i">
                                        <i :class="i <= Math.floor(restaurant.rating) ? 'fas fa-star' : (i - 0.5 <= restaurant.rating ? 'fas fa-star-half-alt' : 'far fa-star')"></i>
                                    </template>
                                </div>
                                <span class="ml-2 text-gray-600" x-text="restaurant.rating.toFixed(1)"></span>
                                <span class="ml-2 text-gray-400" x-text="`(${restaurant.total_ratings} reviews)`"></span>
                                
                                <!-- Reviews Button -->
                                <button @click="viewReviews(restaurant.id)" 
                                        class="ml-3 text-xs text-indigo-600 hover:text-indigo-800 flex items-center focus:outline-none">
                                    View Reviews <i class="fas fa-chevron-right text-xs ml-1"></i>
                                </button>
                            </div>
                            
                            <div x-show="restaurant.total_ratings > 0" 
                                 class="mt-2 flex items-center text-xs text-gray-500">
                                <span class="mr-3"><span class="font-medium">Quality:</span> <span x-text="restaurant.avg_quality?.toFixed(1) || '0.0'"></span></span>
                                <span class="mr-3"><span class="font-medium">Delivery:</span> <span x-text="restaurant.avg_delivery?.toFixed(1) || '0.0'"></span></span>
                                <span><span class="font-medium">Value:</span> <span x-text="restaurant.avg_value?.toFixed(1) || '0.0'"></span></span>
                            </div>
                            
                            <p class="mt-2 text-gray-600 text-sm line-clamp-2" x-text="restaurant.description || 'No description available'"></p>
                            
                            <div class="mt-2 flex flex-wrap gap-1">
                                <template x-for="cuisine in (restaurant.cuisine_types || [])" :key="cuisine">
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded" x-text="cuisine"></span>
                                </template>
                            </div>
                        </div>
                        <div class="text-right ml-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-clock mr-1"></i>
                                <span x-text="`${restaurant.estimated_delivery_minutes} min`"></span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-900 font-semibold">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    <span x-text="`${restaurant.distance_km} km away`"></span>
                                </span>
                                <p class="text-sm text-gray-500" x-text="restaurant.city"></p>
                            </div>
                            <div class="flex space-x-2">
                                <!-- Reviews Button -->
                                <button @click="viewReviews(restaurant.id)" 
                                        class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                    <i class="fas fa-star mr-1"></i>Reviews
                                </button>
                                <!-- View Menu Button -->
                                <button @click="viewRestaurant(restaurant.id)"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm cursor-pointer">
                                    View Menu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Load More Button -->
    <div x-show="currentPage < lastPage" class="mt-8 text-center">
        <button @click="loadMore()" 
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-spinner mr-2" x-show="isLoading"></i>
            Load More Restaurants
        </button>
    </div>

    <!-- Empty State -->
    <div x-show="!isLoading && restaurants.length === 0" x-cloak class="text-center py-12">
        <div class="text-gray-300 text-6xl mb-4">
            <i class="fas fa-search"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No restaurants found</h3>
        <p class="mt-2 text-gray-500">Try adjusting your search or filters</p>
        <button @click="searchQuery = ''; selectedMealType = ''; loadRestaurants()" 
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i class="fas fa-times mr-2"></i>Clear Filters
        </button>
    </div>
</div>