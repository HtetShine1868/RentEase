@php
    $statusColors = [
        'PENDING' => 'bg-yellow-100 text-yellow-800',
        'PICKUP_SCHEDULED' => 'bg-blue-100 text-blue-800',
        'PICKED_UP' => 'bg-purple-100 text-purple-800',
        'IN_PROGRESS' => 'bg-indigo-100 text-indigo-800',
        'READY' => 'bg-green-100 text-green-800',
        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
        'DELIVERED' => 'bg-gray-100 text-gray-800',
        'CANCELLED' => 'bg-red-100 text-red-800'
    ];
    
    $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
    
    // Calculate time left for pickup
    if (in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED']) && $order->pickup_time) {
        $now = now();
        $pickupTime = \Carbon\Carbon::parse($order->pickup_time);
        $hoursLeft = $now->diffInHours($pickupTime, false);
        $minutesLeft = $now->diffInMinutes($pickupTime, false) % 60;
        
        if ($hoursLeft < 0) {
            $timeLeft = '<span class="text-red-600 font-bold">OVERDUE</span>';
            $timeClass = 'bg-red-100 text-red-800';
        } elseif ($hoursLeft < 1) {
            $timeLeft = '<span class="text-orange-600 font-bold">' . $minutesLeft . ' min left</span>';
            $timeClass = 'bg-orange-100 text-orange-800';
        } elseif ($hoursLeft < 3) {
            $timeLeft = '<span class="text-yellow-600">' . $hoursLeft . 'h ' . $minutesLeft . 'm left</span>';
            $timeClass = 'bg-yellow-100 text-yellow-800';
        } else {
            $timeLeft = '<span class="text-green-600">' . $hoursLeft . 'h ' . $minutesLeft . 'm left</span>';
            $timeClass = 'bg-green-100 text-green-800';
        }
    }
    
    // Calculate progress based on status
    $progressMap = [
        'PENDING' => 0,
        'PICKUP_SCHEDULED' => 10,
        'PICKED_UP' => 25,
        'IN_PROGRESS' => 50,
        'READY' => 75,
        'OUT_FOR_DELIVERY' => 90,
        'DELIVERED' => 100
    ];
    $progress = $progressMap[$order->status] ?? 0;
    
    // Safely get order items
    $orderItems = $order->orderItems ?? collect([]);
    $totalItems = $orderItems->sum('quantity');
    $itemsCount = $orderItems->count();
@endphp

<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border-l-4 
    {{ $order->is_rush ? 'border-orange-500' : 'border-blue-500' }} p-4 mb-3 order-card"
     data-status="{{ $order->status }}"
     data-order-id="{{ $order->id }}">
    
    <div class="flex flex-wrap lg:flex-nowrap items-start justify-between gap-4">
        {{-- Left Section - Order Info --}}
        <div class="flex-1 min-w-[250px]">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($order->is_rush)
                    <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-bold flex items-center">
                        <i class="fas fa-bolt mr-1"></i> RUSH
                    </span>
                @endif
                <span class="font-bold text-lg text-gray-800">#{{ $order->order_reference }}</span>
                <span class="px-2 py-1 rounded-full text-xs {{ $statusColor }}">
                    {{ str_replace('_', ' ', $order->status) }}
                </span>
                @if(isset($timeLeft))
                    <span class="text-xs px-2 py-1 rounded-full {{ $timeClass ?? 'bg-gray-100' }}">
                        {!! $timeLeft !!}
                    </span>
                @endif
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <div>
                    <p class="text-gray-500 text-xs flex items-center">
                        <i class="fas fa-user mr-1"></i> Customer
                    </p>
                    <p class="font-medium text-gray-800">{{ $order->user->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500">{{ $order->user->phone ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <p class="text-gray-500 text-xs flex items-center">
                        <i class="fas fa-box mr-1"></i> Items
                    </p>
                    <p class="font-medium text-gray-800">{{ $totalItems }} items</p>
                    @if($itemsCount > 0)
                        @foreach($orderItems->take(2) as $item)
                            <p class="text-xs text-gray-600">{{ $item->quantity }}x {{ $item->laundryItem->item_name ?? 'Item' }}</p>
                        @endforeach
                        @if($itemsCount > 2)
                            <p class="text-xs text-gray-400">+{{ $itemsCount - 2 }} more</p>
                        @endif
                    @else
                        <p class="text-xs text-gray-400">No items</p>
                    @endif
                </div>
                
                <div>
                    <p class="text-gray-500 text-xs flex items-center">
                        <i class="fas fa-clock mr-1"></i> Pickup
                    </p>
                    <p class="font-medium text-gray-800">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') : 'N/A' }}</p>
                    <p class="text-xs text-gray-500">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('M j, Y') : 'N/A' }}</p>
                </div>
                
                <div>
                    <p class="text-gray-500 text-xs flex items-center">
                        <i class="fas fa-calendar-check mr-1"></i> Deliver By
                    </p>
                    <p class="font-medium text-gray-800">{{ $order->expected_return_date ? \Carbon\Carbon::parse($order->expected_return_date)->format('M j, Y') : 'N/A' }}</p>
                    @if($order->expected_return_date && \Carbon\Carbon::parse($order->expected_return_date)->isToday())
                        <p class="text-xs text-orange-600 font-medium">Today</p>
                    @endif
                </div>
                
                <div class="col-span-2 md:col-span-1">
                    <p class="text-gray-500 text-xs flex items-center">
                        <i class="fas fa-map-marker-alt mr-1"></i> Location
                    </p>
                    <p class="font-medium text-gray-800 text-sm truncate" title="{{ $order->pickup_address ?? 'N/A' }}">
                        {{ Str::limit($order->pickup_address ?? 'N/A', 30) }}
                    </p>
                    @if($order->distance_km)
                        <p class="text-xs text-gray-500">{{ $order->distance_km }} km away</p>
                    @endif
                </div>
            </div>
            
            @if($order->pickup_instructions)
            <div class="mt-2 text-xs bg-yellow-50 p-2 rounded-lg text-yellow-800">
                <i class="fas fa-info-circle mr-1"></i> {{ $order->pickup_instructions }}
            </div>
            @endif
        </div>
        
        {{-- Right Section - Actions & Pricing --}}
        <div class="flex flex-col items-end gap-3 min-w-[180px]">
            <div class="text-right w-full">
                <p class="text-gray-500 text-xs">Total Amount</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($order->total_amount, 2) }}</p>
                <p class="text-xs text-gray-500">
                    Base: ${{ number_format($order->base_amount, 2) }}
                    @if($order->rush_surcharge > 0)
                        <span class="text-orange-600">+ Rush</span>
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap gap-2 justify-end w-full">
                {{-- Status-based action buttons --}}
                @if($order->status == 'PENDING')
                    <button class="accept-order-btn px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-check mr-1"></i> Accept
                    </button>
                @endif
                
                @if($order->status == 'PICKUP_SCHEDULED')
                    <button class="mark-picked-up-btn px-4 py-2 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-box-open mr-1"></i> Picked Up
                    </button>
                @endif
                @if($order->status == 'PICKED_UP')
                    <button class="start-processing-btn px-4 py-2 bg-indigo-500 text-white text-sm rounded-lg hover:bg-indigo-600 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-play mr-1"></i> Start Processing
                    </button>
                @endif
                
                @if($order->status == 'IN_PROGRESS')
                    <button class="mark-ready-btn px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-check-circle mr-1"></i> Mark Ready
                    </button>
                @endif
                
                @if($order->status == 'READY')
                    <button class="out-for-delivery-btn px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-truck mr-1"></i> Out for Delivery
                    </button>
                @endif
                
                @if($order->status == 'OUT_FOR_DELIVERY')
                    <button class="delivered-btn px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-check-double mr-1"></i> Delivered
                    </button>
                @endif
                
                {{-- Common buttons --}}
         
                
                <button class="view-details-btn px-3 py-2 bg-blue-50 text-blue-600 text-sm rounded-lg hover:bg-blue-100 transition-colors flex items-center"
                        data-id="{{ $order->id }}">
                    <i class="fas fa-eye mr-1"></i> Details
                </button>
                
                @if(in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED']))
                    <button class="cancel-order-btn px-3 py-2 bg-red-50 text-red-600 text-sm rounded-lg hover:bg-red-100 transition-colors flex items-center"
                            data-id="{{ $order->id }}">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                @endif
            </div>
            
            @if(in_array($order->status, ['PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY']))
                <div class="w-full mt-2">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Progress</span>
                        <span class="font-medium">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $order->status == 'OUT_FOR_DELIVERY' ? 'orange' : 'blue' }}-500 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>