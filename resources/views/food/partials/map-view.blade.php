<!-- Map View Modal -->
<div x-show="showMapModal" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showMapModal = false"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Select Delivery Location
                            </h3>
                            <button @click="showMapModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Search Location -->
                        <div class="mb-4 relative">
                            <div class="relative">
                                <input type="text" 
                                       x-model="locationSearch"
                                       @input.debounce.500ms="searchLocation()"
                                       @focus="showSearchResults = true"
                                       placeholder="Search for an area or address..."
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute left-3 top-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <div x-show="locationSearch" 
                                     @click="locationSearch = ''; searchResults = []"
                                     class="absolute right-3 top-3 cursor-pointer">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Search Results -->
                            <div x-show="searchResults.length > 0 && showSearchResults" 
                                 x-cloak
                                 @click.away="showSearchResults = false"
                                 class="absolute z-20 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
                                <template x-for="result in searchResults" :key="result.place_id">
                                    <div @click="selectSearchResult(result)"
                                         class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition">
                                        <div class="flex items-start">
                                            <svg class="h-5 w-5 text-gray-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <div class="font-medium text-gray-900" x-text="result.display_name"></div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <span class="capitalize" x-text="result.type"></span>
                                                    <span class="mx-1">•</span>
                                                    <span class="capitalize" x-text="result.class"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Map Container -->
                        <div id="map" class="w-full h-96 rounded-lg border-2 border-gray-300 mb-4 relative">
                            <!-- Loading Overlay -->
                            <div x-show="!mapInitialized" 
                                 class="absolute inset-0 bg-gray-100 bg-opacity-75 flex items-center justify-center rounded-lg z-10">
                                <div class="text-center">
                                    <div class="loading-spinner mb-2"></div>
                                    <p class="text-sm text-gray-600">Loading map...</p>
                                </div>
                            </div>
                            <!-- Error Overlay -->
                            <div x-show="mapError" 
                                 x-cloak
                                 class="absolute inset-0 bg-red-50 bg-opacity-75 flex items-center justify-center rounded-lg z-10">
                                <div class="text-center">
                                    <svg class="h-12 w-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-red-600 mb-2">Failed to load map</p>
                                    <button @click="initMap()" 
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Retry
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Map Controls -->
                        <div class="absolute bottom-28 right-8 space-y-2 z-20">
                            <!-- Current Location Button -->
                            <button @click="getCurrentLocation()" 
                                    class="bg-white rounded-full p-3 shadow-lg hover:bg-gray-50 focus:outline-none transition transform hover:scale-110">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                            
                            <!-- Zoom In Button -->
                            <button @click="if(map) map.zoomIn()" 
                                    class="bg-white rounded-full p-3 shadow-lg hover:bg-gray-50 focus:outline-none transition transform hover:scale-110 block">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                            
                            <!-- Zoom Out Button -->
                            <button @click="if(map) map.zoomOut()" 
                                    class="bg-white rounded-full p-3 shadow-lg hover:bg-gray-50 focus:outline-none transition transform hover:scale-110 block">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Selected Location Info -->
                        <div x-show="selectedLocation" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="bg-indigo-50 p-4 rounded-lg mt-4 border border-indigo-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-indigo-900">Selected Delivery Location</h4>
                                    <p class="text-sm text-indigo-800 mt-1" x-text="selectedLocation?.address || 'Loading...'"></p>
                                    <div class="mt-2 flex items-center text-xs text-indigo-700">
                                        <span>Lat: <span x-text="selectedLocation?.lat?.toFixed(6) || '0.000000'"></span></span>
                                        <span class="mx-2">•</span>
                                        <span>Lng: <span x-text="selectedLocation?.lng?.toFixed(6) || '0.000000'"></span></span>
                                    </div>
                                </div>
                                <button @click="selectedLocation = null; if(marker) marker.remove()" 
                                        class="text-indigo-500 hover:text-indigo-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <button @click="useSavedAddress()" 
                                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                                Use Saved Address
                            </button>
                            <button @click="getCurrentLocation()" 
                                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                My Current Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        @click="confirmLocation()"
                        :disabled="!selectedLocation"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirm Location
                </button>
                <button type="button" 
                        @click="showMapModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>