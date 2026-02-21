<!-- Reviews Modal -->
<div x-show="showReviewsModal" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showReviewsModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white" 
                            x-text="selectedRestaurantForReviews?.business_name + ' Reviews'"></h3>
                        <button @click="showReviewsModal = false" class="text-white hover:text-indigo-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Reviews Content -->
                <div class="p-6 max-h-[600px] overflow-y-auto">
                    <!-- Loading State -->
                    <div x-show="isLoadingReviews" class="text-center py-8">
                        <div class="loading-spinner"></div>
                        <p class="mt-4 text-gray-600">Loading reviews...</p>
                    </div>

                    <template x-if="!isLoadingReviews && reviewsData">
                        <div>
                            <!-- Rating Summary -->
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="text-3xl font-bold text-gray-900 mr-2" 
                                                  x-text="reviewsData.stats.average"></span>
                                            <div class="flex">
                                                <template x-for="i in 5" :key="i">
                                                    <svg class="h-5 w-5" 
                                                         :class="i <= Math.round(reviewsData.stats.average) ? 'text-yellow-400' : 'text-gray-300'"
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1" 
                                           x-text="'Based on ' + reviewsData.stats.total + ' reviews'"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Quality: <span class="font-medium" x-text="reviewsData.stats.quality_avg"></span></p>
                                        <p class="text-sm text-gray-600">Delivery: <span class="font-medium" x-text="reviewsData.stats.delivery_avg"></span></p>
                                        <p class="text-sm text-gray-600">Value: <span class="font-medium" x-text="reviewsData.stats.value_avg"></span></p>
                                    </div>
                                </div>

                                <!-- Rating Breakdown -->
                                <div class="space-y-2">
                                    <template x-for="star in [5,4,3,2,1]" :key="star">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-600 w-8" x-text="star + ' ★'"></span>
                                            <div class="flex-1 mx-2">
                                                <div class="h-2 bg-gray-200 rounded-full">
                                                    <div class="h-2 bg-yellow-400 rounded-full" 
                                                         :style="'width: ' + (reviewsData.stats.breakdown[star] / reviewsData.stats.total * 100) + '%'"></div>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-12 text-right" 
                                                  x-text="reviewsData.stats.breakdown[star]"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Reviews List -->
                            <div class="space-y-4">
                                <template x-for="rating in reviewsData.ratings" :key="rating.id">
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                    <span class="text-indigo-600 font-medium" 
                                                          x-text="rating.user_name?.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="font-medium text-gray-900" x-text="rating.user_name"></p>
                                                    <p class="text-xs text-gray-500" x-text="rating.created_at_formatted"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900 mr-1" 
                                                      x-text="rating.overall_rating"></span>
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Rating Breakdown -->
                                        <div class="flex space-x-4 mb-3 text-xs">
                                            <span class="text-gray-600">Quality: <span class="font-medium" x-text="rating.quality_rating"></span></span>
                                            <span class="text-gray-600">Delivery: <span class="font-medium" x-text="rating.delivery_rating"></span></span>
                                            <span class="text-gray-600">Value: <span class="font-medium" x-text="rating.value_rating"></span></span>
                                        </div>

                                        <!-- Comment -->
                                        <p x-show="rating.comment" 
                                           class="text-gray-700 text-sm" 
                                           x-text="rating.comment"></p>

                                        <!-- Helpful Button -->
                                        <div class="mt-3 flex justify-end">
                                            <button @click="markHelpful(rating.id)"
                                                    class="text-xs text-gray-500 hover:text-indigo-600 flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                                </svg>
                                                Helpful
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- No Reviews -->
                            <div x-show="reviewsData.ratings.length === 0" 
                                 class="text-center py-8">
                                <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-500">No reviews yet</p>
                                <p class="text-sm text-gray-400 mt-1">Be the first to review this restaurant!</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-3 flex justify-end">
                    <button type="button" 
                            @click="showReviewsModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>