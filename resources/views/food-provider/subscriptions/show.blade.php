@extends('layouts.food-provider')

@section('title', 'Subscription #' . str_pad($subscription->id, 6, '0', STR_PAD_LEFT))

@section('header', 'Subscription Details')

@section('content')
<div class="space-y-6">
    <!-- Header with back button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('food-provider.subscriptions.index') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Subscription #SUB-{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
            </h2>
            @php
                $statusColors = [
                    'ACTIVE' => 'bg-green-100 text-green-800',
                    'PAUSED' => 'bg-yellow-100 text-yellow-800',
                    'CANCELLED' => 'bg-red-100 text-red-800',
                    'COMPLETED' => 'bg-gray-100 text-gray-800'
                ];
            @endphp
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $subscription->status }}
            </span>
        </div>
        <div class="flex space-x-3">
            @if($subscription->status == 'ACTIVE')
                <button type="button" 
                        onclick="updateStatus({{ $subscription->id }}, 'PAUSED')"
                        class="inline-flex items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50">
                    <i class="fas fa-pause mr-2"></i>
                    Pause Subscription
                </button>
            @endif
            
            @if($subscription->status == 'PAUSED')
                <button type="button" 
                        onclick="updateStatus({{ $subscription->id }}, 'ACTIVE')"
                        class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <i class="fas fa-play mr-2"></i>
                    Resume Subscription
                </button>
            @endif
            
            @if(in_array($subscription->status, ['ACTIVE', 'PAUSED']))
                <button type="button" 
                        onclick="updateStatus({{ $subscription->id }}, 'CANCELLED')"
                        class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <i class="fas fa-times mr-2"></i>
                    Cancel Subscription
                </button>
            @endif
        </div>
    </div>

    <!-- Customer Information -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-800 font-medium text-xl">
                            {{ substr($subscription->user->name ?? 'NA', 0, 2) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $subscription->user->name ?? 'N/A' }}</h4>
                        <p class="text-sm text-gray-500">{{ $subscription->user->email ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $subscription->user->phone ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Default Delivery Address</h4>
                    @php
                        $defaultAddress = $subscription->user->addresses()->where('is_default', true)->first();
                    @endphp
                    @if($defaultAddress)
                        <p class="text-sm text-gray-900">{{ $defaultAddress->address_line1 }}</p>
                        @if($defaultAddress->address_line2)
                            <p class="text-sm text-gray-900">{{ $defaultAddress->address_line2 }}</p>
                        @endif
                        <p class="text-sm text-gray-900">
                            {{ $defaultAddress->city }}, {{ $defaultAddress->state }} - {{ $defaultAddress->postal_code }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $defaultAddress->country }}</p>
                    @else
                        <p class="text-sm text-red-600">No default address set</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Details -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Plan Details -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Plan Details</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meal Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $subscription->mealType->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Delivery Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $subscription->delivery_time ? date('h:i A', strtotime($subscription->delivery_time)) : 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $subscription->start_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $subscription->end_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Daily Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">MMK {{ number_format($subscription->daily_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">MMK {{ number_format($subscription->total_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Discount</dt>
                        <dd class="mt-1 text-sm text-gray-900">MMK {{ number_format($subscription->discount_amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $subscription->start_date->diffInDays($subscription->end_date) + 1 }} days</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Delivery Schedule -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Schedule</h3>
                <div class="grid grid-cols-7 gap-2 mb-4">
                    @php
                        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    @endphp
                    @foreach($days as $index => $day)
                        @php
                            $isActive = $subscription->delivery_days & pow(2, $index);
                        @endphp
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 mb-1">{{ $day }}</div>
                            <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ $isActive ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                <i class="fas {{ $isActive ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <h4 class="text-sm font-medium text-gray-700 mb-2">Delivery Days:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($deliveryDays as $day)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                            {{ $day }}
                        </span>
                    @endforeach
                </div>
                
                @php
                    $today = now()->startOfDay();
                    $isTodayDeliverable = $subscription->status == 'ACTIVE' &&
                                          $subscription->start_date <= $today &&
                                          $subscription->end_date >= $today &&
                                          ($subscription->delivery_days & pow(2, $today->dayOfWeek));
                @endphp
                
                @if($isTodayDeliverable)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            <p class="text-sm text-blue-700">
                                This subscription is scheduled for delivery today at {{ $subscription->delivery_time ? date('h:i A', strtotime($subscription->delivery_time)) : 'scheduled time' }}.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order History</h3>
        </div>
        
        @if($orders->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-shopping-bag text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-1 text-sm text-gray-500">Orders will appear here once they are generated.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Order Reference
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Meal Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Delivery Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $order->order_reference }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->meal_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₹{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $order->estimated_delivery_time ? $order->estimated_delivery_time->format('h:i A') : 'N/A' }}
                                    </div>
                                    @if($order->actual_delivery_time)
                                        <div class="text-xs text-gray-500">
                                            Delivered: {{ $order->actual_delivery_time->format('h:i A') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('food-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateStatus(subscriptionId, newStatus) {
    let message = '';
    switch(newStatus) {
        case 'PAUSED':
            message = 'Are you sure you want to pause this subscription?';
            break;
        case 'ACTIVE':
            message = 'Are you sure you want to resume this subscription?';
            break;
        case 'CANCELLED':
            message = 'Are you sure you want to cancel this subscription? This action cannot be undone.';
            break;
    }
    
    if (!confirm(message)) {
        return;
    }
    
    fetch(`/food-provider/subscriptions/${subscriptionId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating subscription status');
    });
}
</script>
@endpush
@endsection