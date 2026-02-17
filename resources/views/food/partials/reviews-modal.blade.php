<!-- Reviews Modal -->
<div x-show="showReviewsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showReviewsModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="selectedRestaurantForReviews?.business_name"></h3>
                        <p class="text-sm text-gray-500 mt-1">Customer Reviews & Ratings</p>
                    </div>
                    <button @click="showReviewsModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                <!-- Loading State -->
                <div x-show="isLoadingReviews" class="text-center py-12">
                    <div class="loading-spinner"></div>
                    <p class="mt-4 text-gray-600">Loading reviews...</p>
                </div>
                
                <!-- Reviews Content -->
                <div x-show="!isLoadingReviews">
                    <!-- Rating Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6" x-show="reviewsData">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Overall Rating -->
                            <div class="text-center md:border-r border-gray-200">
                                <div class="text-4xl font-bold text-indigo-600" x-text="reviewsData?.stats?.average?.toFixed(1) || '0.0'"></div>
                                <div class="flex justify-center mt-2 text-yellow-400">
                                    <template x-for="i in 5" :key="i">
                                        <i :class="i <= Math.floor(reviewsData?.stats?.average || 0) ? 'fas fa-star' : (i - 0.5 <= (reviewsData?.stats?.average || 0) ? 'fas fa-star-half-alt' : 'far fa-star')"></i>
                                    </template>
                                </div>
                                <div class="mt-1 text-sm text-gray-500" x-text="`${reviewsData?.stats?.total || 0} reviews`"></div>
                            </div>
                            
                            <!-- Rating Breakdown -->
                            <div class="md:col-span-2">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <span class="w-20 text-sm text-gray-600">Quality</span>
                                        <div class="flex-1 mx-4">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" :style="`width: ${(reviewsData?.stats?.quality_avg || 0) * 20}%`"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium" x-text="(reviewsData?.stats?.quality_avg || 0).toFixed(1)"></span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-20 text-sm text-gray-600">Delivery</span>
                                        <div class="flex-1 mx-4">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" :style="`width: ${(reviewsData?.stats?.delivery_avg || 0) * 20}%`"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium" x-text="(reviewsData?.stats?.delivery_avg || 0).toFixed(1)"></span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-20 text-sm text-gray-600">Value</span>
                                        <div class="flex-1 mx-4">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" :style="`width: ${(reviewsData?.stats?.value_avg || 0) * 20}%`"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium" x-text="(reviewsData?.stats?.value_avg || 0).toFixed(1)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Star Distribution -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Rating Distribution</h4>
                            <div class="space-y-2">
                                <template x-for="star in [5,4,3,2,1]" :key="star">
                                    <div class="flex items-center">
                                       <span class="text-sm text-gray-600 w-12" x-text="star + ' star'"></span>
                                        <div class="flex-1 mx-4">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" 
                                                     :style="`width: ${reviewsData?.stats?.total ? (reviewsData.stats.breakdown[star] / reviewsData.stats.total) * 100 : 0}%`"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500 w-12" x-text="reviewsData?.stats?.breakdown[star] || 0"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reviews List -->
                    <div class="space-y-6">
                        <h4 class="font-medium text-gray-900">Customer Reviews</h4>
                        
                        <template x-if="reviewsData?.ratings?.length === 0">
                            <div class="text-center py-8">
                                <i class="fas fa-star text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No reviews yet for this restaurant</p>
                            </div>
                        </template>
                        
                        <template x-for="review in reviewsData?.ratings" :key="review.id">
                            <div class="border-b border-gray-100 last:border-0 pb-6 last:pb-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <template x-if="review.user?.avatar_url">
                                                <img :src="review.user.avatar_url" class="h-10 w-10 rounded-full object-cover">
                                            </template>
                                            <template x-if="!review.user?.avatar_url">
                                                <span class="text-indigo-600 font-semibold" x-text="review.user?.name?.charAt(0) || 'U'"></span>
                                            </template>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900" x-text="review.user?.name || 'Anonymous'"></h4>
                                            <div class="flex items-center mt-1">
                                                <div class="flex text-yellow-400">
                                                    <template x-for="i in 5" :key="i">
                                                        <i :class="i <= review.overall_rating ? 'fas fa-star text-xs' : 'far fa-star text-xs'"></i>
                                                    </template>
                                                </div>
                                                <span class="ml-2 text-xs text-gray-500" x-text="new Date(review.created_at).toLocaleDateString()"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Detailed Ratings -->
                                <div class="mt-3 grid grid-cols-3 gap-2 max-w-md">
                                    <div class="text-xs">
                                        <span class="text-gray-500">Quality:</span>
                                        <span class="ml-1 font-medium" x-text="review.quality_rating + '/5'"></span>
                                    </div>
                                    <div class="text-xs">
                                        <span class="text-gray-500">Delivery:</span>
                                        <span class="ml-1 font-medium" x-text="review.delivery_rating + '/5'"></span>
                                    </div>
                                    <div class="text-xs">
                                        <span class="text-gray-500">Value:</span>
                                        <span class="ml-1 font-medium" x-text="review.value_rating + '/5'"></span>
                                    </div>
                                </div>
                                
                                <p x-show="review.comment" class="mt-3 text-sm text-gray-700" x-text="review.comment"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <button @click="showReviewsModal = false" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>