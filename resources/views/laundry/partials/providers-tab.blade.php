<div>
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.500ms="loadProviders()"
                           placeholder="Search providers or services..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <select x-model="selectedItemType" @change="loadProviders()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 min-w-[150px]">
                    <option value="">All Services</option>
                    @foreach($itemTypes as $type)
                    <option value="{{ $type['name'] }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
                <select x-model="sortBy" @change="loadProviders()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 min-w-[150px]">
                    <option value="rating">Rating</option>
                    <option value="distance">Distance</option>
                    <option value="turnaround">Fastest Turnaround</option>
                    <option value="total_orders">Most Orders</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Provider Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Initial providers from server -->
        @foreach($initialProviders as $provider)
        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <div class="h-48 bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center relative">
                <i class="fas fa-tshirt text-indigo-300 text-5xl"></i>
                @if(isset($provider->in_service_area) && $provider->in_service_area)
                <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i>In Service Area
                </span>
                @endif
            </div>
            
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900">{{ $provider->business_name }}</h3>
                        <div class="flex items-center mt-1">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($provider->rating ?? 0))
                                    <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= ($provider->rating ?? 0))
                                    <i class="fas fa-star-half-alt"></i>
                                    @else
                                    <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-gray-600">{{ number_format($provider->rating ?? 0, 1) }}</span>
                            <span class="ml-2 text-gray-400">({{ $provider->total_orders ?? 0 }} orders)</span>
                        </div>
                        <p class="mt-2 text-gray-600 text-sm line-clamp-2">{{ Str::limit($provider->description ?? 'No description available', 100) }}</p>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div class="bg-gray-50 p-2 rounded text-center">
                        <span class="text-gray-500">Normal</span>
                        <p class="font-semibold">{{ floor(($provider->laundryConfig->normal_turnaround_hours ?? 120) / 24) }} days</p>
                    </div>
                    <div class="bg-gray-50 p-2 rounded text-center">
                        <span class="text-gray-500">Rush</span>
                        <p class="font-semibold">{{ floor(($provider->laundryConfig->rush_turnaround_hours ?? 48) / 24) }} days</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-900 font-semibold">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                {{ $provider->distance_km ?? '5.0' }} km away
                            </span>
                            <p class="text-sm text-gray-500">{{ $provider->city ?? 'Dhaka' }}</p>
                        </div>
                        <button @click="handleButtonClick($event, () => viewProvider({{ $provider->id }}))"
                                class="btn bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 hover-lift transition-all duration-200 text-sm">
                            <span class="flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                View Services
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Dynamic providers from AJAX -->
        <template x-for="provider in providers" :key="provider.id">
            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <div class="h-48 bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center relative">
                    <i class="fas fa-tshirt text-indigo-300 text-5xl"></i>
                    <span x-show="provider.in_service_area" 
                          class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>In Service Area
                    </span>
                </div>
                
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900" x-text="provider.business_name"></h3>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <template x-for="i in 5" :key="i">
                                        <i :class="i <= Math.floor(provider.rating) ? 'fas fa-star' : (i - 0.5 <= provider.rating ? 'fas fa-star-half-alt' : 'far fa-star')"></i>
                                    </template>
                                </div>
                                <span class="ml-2 text-gray-600" x-text="provider.rating.toFixed(1)"></span>
                                <span class="ml-2 text-gray-400" x-text="`(${provider.total_orders} orders)`"></span>
                            </div>
                            <p class="mt-2 text-gray-600 text-sm line-clamp-2" x-text="provider.description || 'No description available'"></p>
                        </div>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-gray-50 p-2 rounded text-center">
                            <span class="text-gray-500">Normal</span>
                            <p class="font-semibold" x-text="Math.floor(provider.normal_turnaround_hours / 24) + ' days'"></p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded text-center">
                            <span class="text-gray-500">Rush</span>
                            <p class="font-semibold" x-text="Math.floor(provider.rush_turnaround_hours / 24) + ' days'"></p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-900 font-semibold">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    <span x-text="`${provider.distance_km} km away`"></span>
                                </span>
                                <p class="text-sm text-gray-500" x-text="provider.city"></p>
                            </div>
                            <button @click="handleButtonClick($event, () => viewProvider(provider.id))"
                                    class="btn bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 hover-lift transition-all duration-200 text-sm">
                                <span class="flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Services
                                </span>
                            </button>
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
            Load More Providers
        </button>
    </div>

    <!-- Empty State -->
    <div x-show="providers.length === 0 && {{ count($initialProviders) }} === 0" x-cloak class="text-center py-12">
        <div class="text-gray-300 text-6xl mb-4">
            <i class="fas fa-tshirt"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No laundry providers found</h3>
        <p class="mt-2 text-gray-500">Try adjusting your search or filters</p>
        <button @click="searchQuery = ''; selectedItemType = ''; loadProviders()" 
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Clear Filters
        </button>
    </div>
</div>