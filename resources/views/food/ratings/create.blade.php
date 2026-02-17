@extends('dashboard')

@section('title', 'Rate Your Order')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white">Rate Your Order</h2>
            <p class="text-indigo-100 text-sm mt-1">Share your experience to help others</p>
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

            <!-- Order Items -->
            <div class="mt-4 bg-white rounded-lg p-4 border border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Items Ordered:</h4>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->foodItem->name }} × {{ $item->quantity }}</span>
                        <span class="font-medium">৳{{ number_format($item->total_price, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Rating Form -->
        <form action="{{ route('food.rate.store', $order) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <!-- Quality Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Food Quality <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="quality_rating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="quality_rating" id="quality_rating" required>
                @error('quality_rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">How was the taste and quality of food?</p>
            </div>

            <!-- Delivery Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Delivery Service <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="delivery_rating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="delivery_rating" id="delivery_rating" required>
                @error('delivery_rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Was the delivery on time and professional?</p>
            </div>

            <!-- Value Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Value for Money <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2 rating-stars" data-target="value_rating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-star text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-value="{{ $i }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="value_rating" id="value_rating" required>
                @error('value_rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Was the food worth the price?</p>
            </div>

            <!-- Comment -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Write a Review (Optional)
                </label>
                <textarea name="comment" rows="4" 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Share your experience with this restaurant...">{{ old('comment') }}</textarea>
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Photos (Optional)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload photos</span>
                                <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                    </div>
                </div>
                <div id="image-preview" class="mt-2 flex flex-wrap gap-2"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('food.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle star rating clicks
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
            
            star.addEventListener('mouseenter', function() {
                const value = this.dataset.value;
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.add('text-yellow-300');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                stars.forEach(s => {
                    s.classList.remove('text-yellow-300');
                });
            });
        });
    });

    // Image preview
    const imageInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    
    imageInput.addEventListener('change', function() {
        preview.innerHTML = '';
        for (let file of this.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative w-20 h-20';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                    <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                `;
                div.querySelector('button').addEventListener('click', function() {
                    div.remove();
                });
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

<style>
.rating-star {
    transition: color 0.2s ease;
    cursor: pointer;
}
.rating-star.text-yellow-400 {
    color: #FBBF24;
}
.rating-star.text-yellow-300 {
    color: #FCD34D;
}
</style>
@endsection