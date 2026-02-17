@extends('dashboard')

@section('title', 'Edit Your Review')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white">Edit Your Review</h2>
            <p class="text-indigo-100 text-sm mt-1">Update your feedback for this order</p>
        </div>

        <!-- Order Summary -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $order->serviceProvider->business_name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Order #{{ $order->order_reference }} • 
                        {{ $order->created_at->format('M d, Y') }} • 
                        {{ $order->mealType->name }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-indigo-600">৳{{ number_format($order->total_amount, 2) }}</div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                </div>
            </div>
        </div>

        <!-- Rating Form -->
        <form action="{{ route('food.rate.update', $order) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Quality Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Food Quality <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="quality_rating" data-value="{{ $rating->quality_rating }}">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl {{ $i <= $rating->quality_rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="quality_rating" id="quality_rating" value="{{ $rating->quality_rating }}" required>
            </div>

            <!-- Delivery Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Delivery Service <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="delivery_rating" data-value="{{ $rating->delivery_rating }}">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl {{ $i <= $rating->delivery_rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="delivery_rating" id="delivery_rating" value="{{ $rating->delivery_rating }}" required>
            </div>

            <!-- Value Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Value for Money <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="value_rating" data-value="{{ $rating->value_rating }}">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl {{ $i <= $rating->value_rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="value_rating" id="value_rating" value="{{ $rating->value_rating }}" required>
            </div>

            <!-- Comment -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Write a Review (Optional)
                </label>
                <textarea name="comment" rows="4" 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Share your experience...">{{ old('comment', $rating->comment) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('food.orders') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Update Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rating-stars').forEach(function(container) {
        const stars = container.querySelectorAll('.rating-star');
        const targetId = container.dataset.target;
        const input = document.getElementById(targetId);
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                input.value = value;
                
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    });
});
</script>
@endsection