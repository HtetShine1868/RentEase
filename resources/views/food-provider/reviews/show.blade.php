@extends('layouts.food-provider')

@section('title', 'Review Details')

@section('header', 'Review Details')

@section('content')
<div class="space-y-6">
    <!-- Header with back button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('food-provider.reviews.index') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Review from {{ $review->user->name ?? 'Anonymous' }}
            </h2>
        </div>
        <div class="flex space-x-3">
            <button type="button"
                    onclick="replyToReview()"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-reply mr-2"></i>
                Reply to Review
            </button>
        </div>
    </div>

    <!-- Review Details Card -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <!-- Customer Info -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-800 font-medium text-xl">
                        {{ substr($review->user->name ?? 'NA', 0, 2) }}
                    </span>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</h3>
                    <p class="text-sm text-gray-500">{{ $review->user->email ?? 'No email' }}</p>
                    <p class="text-sm text-gray-500">{{ $review->user->phone ?? 'No phone' }}</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-sm text-gray-500">Reviewed on</p>
                    <p class="text-base font-medium text-gray-900">{{ $review->created_at->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $review->created_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Ratings</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Overall Rating -->
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500 mb-2">Overall</p>
                    <div class="flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $review->overall_rating }}</span>
                        <span class="text-sm text-gray-500 ml-1">/5</span>
                    </div>
                    <div class="flex items-center justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                </div>

                <!-- Quality Rating -->
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500 mb-2">Food Quality</p>
                    <div class="flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $review->quality_rating }}</span>
                        <span class="text-sm text-gray-500 ml-1">/5</span>
                    </div>
                    <div class="flex items-center justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $review->quality_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                </div>

                <!-- Delivery Rating -->
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500 mb-2">Delivery</p>
                    <div class="flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $review->delivery_rating }}</span>
                        <span class="text-sm text-gray-500 ml-1">/5</span>
                    </div>
                    <div class="flex items-center justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $review->delivery_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                </div>

                <!-- Value Rating -->
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500 mb-2">Value for Money</p>
                    <div class="flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $review->value_rating }}</span>
                        <span class="text-sm text-gray-500 ml-1">/5</span>
                    </div>
                    <div class="flex items-center justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $review->value_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Comment -->
        @if($review->comment)
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Review Comment</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700 text-lg italic">"{{ $review->comment }}"</p>
            </div>
        </div>
        @endif

        <!-- Provider Reply -->
        @if($review->provider_reply)
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Your Reply</h4>
            <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-400">
                <p class="text-gray-700">{{ $review->provider_reply }}</p>
                @if($review->replied_at)
                    <p class="text-xs text-gray-500 mt-2">Replied on {{ $review->replied_at->format('F d, Y \a\t h:i A') }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Order Details -->
        @if(isset($order))
        <div class="px-6 py-5">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Order Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Order Reference:</dt>
                            <dd class="font-medium text-gray-900">{{ $order->order_reference }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Order Date:</dt>
                            <dd class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, h:i A') }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Meal Type:</dt>
                            <dd class="font-medium text-gray-900">{{ $order->mealType->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Order Type:</dt>
                            <dd class="font-medium text-gray-900">
                                {{ $order->order_type === 'SUBSCRIPTION_MEAL' ? 'Subscription' : 'Pay-per-eat' }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Total Amount:</dt>
                            <dd class="font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Order Status:</dt>
                            <dd>
                                @php
                                    $statusColors = [
                                        'PENDING' => 'bg-yellow-100 text-yellow-800',
                                        'ACCEPTED' => 'bg-blue-100 text-blue-800',
                                        'PREPARING' => 'bg-purple-100 text-purple-800',
                                        'OUT_FOR_DELIVERY' => 'bg-indigo-100 text-indigo-800',
                                        'DELIVERED' => 'bg-green-100 text-green-800',
                                        'CANCELLED' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Delivery Address:</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $order->delivery_address }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order Items -->
            @if($order->items->isNotEmpty())
                <div class="mt-6">
                    <h5 class="text-md font-medium text-gray-900 mb-3">Order Items</h5>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $item->foodItem->name ?? 'Unknown' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">₹{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function replyToReview() {
    @if($review->provider_reply)
        if (!confirm('You have already replied to this review. Do you want to update your reply?')) {
            return;
        }
    @endif

    const reply = prompt('Enter your reply to this review:', '{{ $review->provider_reply ?? '' }}');
    if (reply && reply.trim()) {
        fetch('{{ route("food-provider.reviews.reply", $review->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reply: reply })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reply sent successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending reply');
        });
    }
}
</script>
@endpush
@endsection