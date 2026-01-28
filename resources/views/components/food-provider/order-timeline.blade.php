@props([
    'currentStatus' => 'placed',
    'showLabels' => true,
    'compact' => false
])

@php
    $steps = [
        'placed' => [
            'icon' => 'fas fa-shopping-cart',
            'title' => 'Order Placed',
            'description' => 'Customer placed the order',
            'color' => 'gray',
            'time' => '10:30 AM'
        ],
        'accepted' => [
            'icon' => 'fas fa-check-circle',
            'title' => 'Order Accepted',
            'description' => 'You accepted the order',
            'color' => 'blue',
            'time' => '10:32 AM'
        ],
        'preparing' => [
            'icon' => 'fas fa-utensils',
            'title' => 'Preparing Food',
            'description' => 'Kitchen is preparing the order',
            'color' => 'yellow',
            'time' => '10:35 AM'
        ],
        'out_for_delivery' => [
            'icon' => 'fas fa-shipping-fast',
            'title' => 'Out for Delivery',
            'description' => 'Order is on the way',
            'color' => 'purple',
            'time' => '11:00 AM'
        ],
        'delivered' => [
            'icon' => 'fas fa-flag-checkered',
            'title' => 'Delivered',
            'description' => 'Order delivered successfully',
            'color' => 'green',
            'time' => '11:15 AM'
        ],
        'delayed' => [
            'icon' => 'fas fa-exclamation-triangle',
            'title' => 'Delayed',
            'description' => 'Order delivery is delayed',
            'color' => 'red',
            'time' => null
        ]
    ];
    
    $statusOrder = ['placed', 'accepted', 'preparing', 'out_for_delivery', 'delivered'];
    $currentIndex = array_search($currentStatus, $statusOrder);
    
    $colors = [
        'gray' => 'bg-gray-500',
        'blue' => 'bg-blue-500',
        'yellow' => 'bg-yellow-500',
        'purple' => 'bg-purple-500',
        'green' => 'bg-green-500',
        'red' => 'bg-red-500'
    ];
@endphp

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @foreach($steps as $key => $step)
            @if(in_array($key, $statusOrder) || $key === 'delayed')
                @php
                    $isCompleted = in_array($key, array_slice($statusOrder, 0, $currentIndex + 1));
                    $isCurrent = $key === $currentStatus;
                    $isUpcoming = !$isCompleted && !$isCurrent;
                    
                    if ($isCompleted) {
                        $statusClass = 'completed';
                        $iconColor = 'text-white';
                        $bgColor = $colors[$step['color']];
                    } elseif ($isCurrent) {
                        $statusClass = 'current';
                        $iconColor = 'text-white';
                        $bgColor = $colors[$step['color']];
                    } else {
                        $statusClass = 'upcoming';
                        $iconColor = 'text-gray-400';
                        $bgColor = 'bg-gray-300';
                    }
                @endphp
                
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 {{ $isCompleted ? 'bg-green-200' : 'bg-gray-200' }}" 
                                  aria-hidden="true"></span>
                        @endif
                        
                        <div class="relative flex items-start space-x-3">
                            <div>
                                <div class="relative flex items-center justify-center h-8 w-8 rounded-full {{ $bgColor }} border-2 border-white shadow">
                                    <i class="{{ $step['icon'] }} text-xs {{ $iconColor }}"></i>
                                    
                                    @if($isCurrent)
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $bgColor }} opacity-75"></span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="min-w-0 flex-1">
                                <div>
                                    <div class="flex justify-between">
                                        <div>
                                            @if($showLabels)
                                                <p class="text-sm font-medium text-gray-900">{{ $step['title'] }}</p>
                                            @endif
                                            @if(!$compact)
                                                <p class="mt-0.5 text-sm text-gray-500">{{ $step['description'] }}</p>
                                            @endif
                                        </div>
                                        @if($step['time'] && $showLabels)
                                            <div class="text-right text-xs text-gray-500">
                                                {{ $step['time'] }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($isCurrent)
                                        <div class="mt-2">
                                            <div class="text-xs inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                <span>Currently at this step</span>
                                            </div>
                                            
                                            @if($key === 'preparing')
                                                <div class="mt-2">
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                            <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                                                        </div>
                                                        <span class="ml-2">60% prepared</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endif
        @endforeach
    </ul>
</div>

@if($compact)
<div class="mt-4 text-center">
    <button type="button" 
            class="text-sm text-indigo-600 hover:text-indigo-500"
            x-data="{ expanded: false }"
            @click="expanded = !expanded">
        <span x-show="!expanded">
            <i class="fas fa-chevron-down mr-1"></i> Show full timeline
        </span>
        <span x-show="expanded" x-cloak>
            <i class="fas fa-chevron-up mr-1"></i> Hide timeline
        </span>
    </button>
</div>
@endif