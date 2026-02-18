<div class="review-card p-6 hover:bg-gray-50">
    <div class="flex items-start">
        <!-- Customer Avatar -->
        <div class="flex-shrink-0 mr-4">
            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg">
                {{ $customerInitials ?? 'JD' }}
            </div>
        </div>
        
        <!-- Review Content -->
        <div class="flex-1">
            <!-- Header -->
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $customerName ?? 'John Doe' }}</h4>
                    <div class="flex items-center mt-1">
                        <!-- Star Rating -->
                        @include('food-provider.reviews.components.rating-stars', ['rating' => $rating ?? 5])
                        <span class="ml-2 text-sm text-gray-500">{{ $date ?? 'Mar 15, 2024' }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                        {{ $orderType ?? 'Subscription' }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                        {{ $mealType ?? 'Lunch' }}
                    </span>
                </div>
            </div>
            
            <!-- Comment -->
            <div class="mt-3">
                <p class="text-gray-700">{{ $comment ?? 'No comment provided.' }}</p>
            </div>
            
            <!-- Response Section (if exists) -->
            @if(isset($response) && $response)
            <div class="mt-4 ml-6 pl-4 border-l-2 border-blue-200">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm mr-2">
                                YR
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-900">Your Response</h5>
                                <p class="text-xs text-gray-500">{{ $responseDate ?? 'Mar 15, 2024' }}</p>
                            </div>
                        </div>
                        <button class="text-sm text-blue-600 hover:text-blue-800" 
                                onclick="editResponse('{{ $reviewId ?? 1 }}')">
                            Edit
                        </button>
                    </div>
                    <p class="text-sm text-gray-700">{{ $response }}</p>
                </div>
            </div>
            @endif
            
           <!-- Actions -->
            <div class="mt-4 flex items-center space-x-4">
                @if(!isset($response) || !$response)
                    <button class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                        <?php
                            $id = $reviewId ?? 1;
                            $name = json_encode($customerName ?? 'John Doe');
                            echo "onclick=\"openResponseModal($id, $name)\"";
                        ?>>
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Respond
                    </button>
                @endif
                <button class="text-sm text-gray-600 hover:text-gray-800"
                        onclick="reportReview('{{ $reviewId ?? 1 }}')">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Report
                </button>
                
                <button class="text-sm text-green-600 hover:text-green-800"
                        onclick="helpfulReview('{{ $reviewId ?? 1 }}')">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905a3.61 3.61 0 01-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    Helpful
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function editResponse(reviewId) {
        // Placeholder for editing response
        console.log('Edit response for review:', reviewId);
        // In production: Load response into modal for editing
    }
    
    function reportReview(reviewId) {
        if (confirm('Are you sure you want to report this review?')) {
            // Placeholder for reporting review
            console.log('Report review:', reviewId);
            // In production: Submit report via API
        }
    }
    
    function helpfulReview(reviewId) {
        // Placeholder for marking review as helpful
        console.log('Mark review as helpful:', reviewId);
        // In production: Submit via API
        alert('Thank you for your feedback!');
    }
</script>