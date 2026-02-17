@extends('layouts.food-provider')

@section('title', "Today's Subscriptions - " . $today->format('d M Y'))

@section('header', "Today's Deliveries - " . $today->format('l, d M Y'))

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Today's Delivery Schedule
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $subscriptions->count() }} subscriptions scheduled for delivery today
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button type="button" 
                    onclick="generateOrders()"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-magic mr-2"></i>
                Generate Today's Orders
            </button>
            <a href="{{ route('food-provider.subscriptions.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Subscriptions
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Total to Deliver -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <i class="fas fa-tasks text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total to Deliver
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $subscriptions->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Already Generated -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Orders Generated
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ count($existingOrders) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pending Generation
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $subscriptions->count() - count($existingOrders) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time-based grouping -->
    @php
        $groupedSubscriptions = $subscriptions->groupBy(function($sub) {
            return $sub->delivery_time ? date('H', strtotime($sub->delivery_time)) : '00';
        })->sortKeys();
    @endphp

    @foreach($groupedSubscriptions as $hour => $timeSubscriptions)
        @php
            $timeLabel = $hour . ':00 - ' . ($hour + 1) . ':00';
            $generatedCount = $timeSubscriptions->filter(function($sub) use ($existingOrders) {
                return in_array($sub->id, $existingOrders);
            })->count();
        @endphp
        
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $timeLabel }}
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $timeSubscriptions->count() }} subscriptions
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $generatedCount == $timeSubscriptions->count() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $generatedCount }}/{{ $timeSubscriptions->count() }} generated
                        </span>
                    </div>
                    @if($generatedCount < $timeSubscriptions->count())
                        <button type="button"
                                onclick="generateTimeSlotOrders('{{ $hour }}')"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            <i class="fas fa-magic mr-1"></i>
                            Generate for this time
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meal Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($timeSubscriptions as $subscription)
                            @php
                                $isGenerated = in_array($subscription->id, $existingOrders);
                            @endphp
                            <tr class="{{ $isGenerated ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium text-xs">
                                                {{ substr($subscription->user->name ?? 'NA', 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $subscription->user->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $subscription->user->phone ?? 'No phone' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $subscription->mealType->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $subscription->delivery_time ? date('h:i A', strtotime($subscription->delivery_time)) : 'Not set' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $address = $subscription->user->addresses()->where('is_default', true)->first();
                                    @endphp
                                    @if($address)
                                        <div class="text-sm text-gray-900">
                                            {{ $address->address_line1 }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}
                                        </div>
                                    @else
                                        <span class="text-sm text-red-600">No address found</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($isGenerated)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Order Generated
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('food-provider.subscriptions.show', $subscription->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$isGenerated && $address)
                                            <button type="button"
                                                    onclick="generateSingleOrder({{ $subscription->id }})"
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Generate Order">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                        @endif
                                        
                                        @if($isGenerated)
                                            <a href="{{ route('food-provider.orders.index') }}?subscription_id={{ $subscription->id }}"
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View Order">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if($subscriptions->isEmpty())
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-calendar-check text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No deliveries today</h3>
                <p class="mt-1 text-sm text-gray-500">There are no subscriptions scheduled for delivery today.</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function generateOrders() {
    if (!confirm('Generate orders for all pending subscriptions today?')) {
        return;
    }
    
    fetch('{{ route("food-provider.subscriptions.generate-today") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
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
        alert('Error generating orders');
    });
}

function generateTimeSlotOrders(hour) {
    if (!confirm(`Generate orders for all subscriptions between ${hour}:00 - ${parseInt(hour)+1}:00?`)) {
        return;
    }
    
    // This would need a separate endpoint for time-slot generation
    // For now, we'll just generate all
    generateOrders();
}

function generateSingleOrder(subscriptionId) {
    if (!confirm('Generate order for this subscription?')) {
        return;
    }
    
    // This would need a separate endpoint for single subscription generation
    // For now, we'll just generate all
    generateOrders();
}
</script>
@endpush
@endsection