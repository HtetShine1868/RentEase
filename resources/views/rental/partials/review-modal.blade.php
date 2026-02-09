<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
            <h3 class="text-lg font-medium text-gray-900">Write Review</h3>
            <p id="reviewPropertyName" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <form id="reviewForm" method="POST" action="{{ route('property-ratings.store') }}">
            @csrf
            <input type="hidden" name="booking_id" id="reviewBookingId">
            <input type="hidden" name="property_id" id="reviewPropertyId">
            
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Ratings -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cleanliness</label>
                            <div class="flex space-x-1" id="cleanlinessRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('cleanliness', {{ $i }})"
                                            class="text-gray-300 hover:text-yellow-400 rating-star transition-colors"
                                            data-rating="{{ $i }}"
                                            data-category="cleanliness">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="cleanliness_rating" id="cleanlinessInput" value="0" required>
                        </div>
                        
                        <!-- ... other rating categories ... -->
                    </div>
                    
                    <!-- Comment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                        <textarea name="comment" rows="4"
                                  class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                  placeholder="Share your experience..."></textarea>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeReviewModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>