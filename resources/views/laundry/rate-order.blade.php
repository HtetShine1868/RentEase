@extends('dashboard')

@section('title', 'Rate Your Laundry Service')
@section('subtitle', 'Share your experience with this provider')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-[#174455]">Rate Your Experience</h3>
            <p class="text-sm text-gray-500">Your feedback helps improve service quality</p>
        </div>

        {{-- Order Info --}}
        <div class="p-6 border-b bg-gray-50">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    @if($order->serviceProvider->avatar_url)
                        <img src="{{ Storage::url($order->serviceProvider->avatar_url) }}" 
                             alt="{{ $order->serviceProvider->business_name }}"
                             class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-[#174455] flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $order->serviceProvider->business_name }}</h4>
                    <p class="text-sm text-gray-600">Order #{{ $order->order_reference }}</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Rating Form --}}
        <div class="p-6">
            <form id="ratingForm">
                @csrf
                
                {{-- Quality Rating --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quality of Service</label>
                    <div class="flex items-center gap-2">
                        <div class="rating-stars" data-rating="quality">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="far fa-star text-2xl text-gray-400 cursor-pointer hover:text-yellow-400" 
                                   data-value="{{ $i }}" data-category="quality"></i>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-500" id="quality-value">Select rating</span>
                    </div>
                    <input type="hidden" name="quality_rating" id="quality_rating" required>
                </div>

                {{-- Delivery Rating --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery/Pickup Experience</label>
                    <div class="flex items-center gap-2">
                        <div class="rating-stars" data-rating="delivery">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="far fa-star text-2xl text-gray-400 cursor-pointer hover:text-yellow-400" 
                                   data-value="{{ $i }}" data-category="delivery"></i>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-500" id="delivery-value">Select rating</span>
                    </div>
                    <input type="hidden" name="delivery_rating" id="delivery_rating" required>
                </div>

                {{-- Value Rating --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Value for Money</label>
                    <div class="flex items-center gap-2">
                        <div class="rating-stars" data-rating="value">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="far fa-star text-2xl text-gray-400 cursor-pointer hover:text-yellow-400" 
                                   data-value="{{ $i }}" data-category="value"></i>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-500" id="value-value">Select rating</span>
                    </div>
                    <input type="hidden" name="value_rating" id="value_rating" required>
                </div>

                {{-- Comment --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Write a Review (Optional)</label>
                    <textarea name="comment" id="comment" rows="4" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                              placeholder="Share your experience with this service..."></textarea>
                </div>

                {{-- Submit Button --}}
                <div class="flex gap-3">
                    <button type="submit" id="submit-btn"
                            class="flex-1 bg-[#174455] text-white py-3 rounded-lg hover:bg-[#1f556b] transition-colors font-medium">
                        Submit Rating
                    </button>
                    <a href="{{ route('laundry.my-orders') }}" 
                       class="flex-1 text-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Star rating functionality
        document.querySelectorAll('.rating-stars i').forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                const category = this.dataset.category;
                const stars = document.querySelectorAll(`.rating-stars[data-rating="${category}"] i`);
                
                // Update stars
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.remove('far', 'text-gray-400');
                        s.classList.add('fas', 'text-yellow-400');
                    } else {
                        s.classList.remove('fas', 'text-yellow-400');
                        s.classList.add('far', 'text-gray-400');
                    }
                });
                
                // Update hidden input
                document.getElementById(`${category}_rating`).value = value;
                
                // Update text
                document.getElementById(`${category}-value`).textContent = `${value} star${value > 1 ? 's' : ''}`;
            });
        });
        
        // Form submission
        document.getElementById('ratingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate ratings
            const quality = document.getElementById('quality_rating').value;
            const delivery = document.getElementById('delivery_rating').value;
            const value = document.getElementById('value_rating').value;
            
            if (!quality || !delivery || !value) {
                alert('Please rate all categories');
                return;
            }
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
            
            fetch('{{ route("laundry.rate.submit", $order->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your rating!');
                    window.location.href = '{{ route("laundry.my-orders") }}';
                } else {
                    alert(data.message || 'Error submitting rating');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting rating');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });
</script>
@endpush
@endsection