@props([
    'title' => '',
    'value' => '',
    'change' => '',
    'icon' => 'fas fa-chart-line',
    'color' => 'blue',
    'loading' => false
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-800',
            'icon' => 'text-blue-500',
            'dark' => 'bg-blue-500'
        ],
        'green' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-800',
            'icon' => 'text-green-500',
            'dark' => 'bg-green-500'
        ],
        'purple' => [
            'bg' => 'bg-purple-100',
            'text' => 'text-purple-800',
            'icon' => 'text-purple-500',
            'dark' => 'bg-purple-500'
        ],
        'yellow' => [
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-800',
            'icon' => 'text-yellow-500',
            'dark' => 'bg-yellow-500'
        ],
        'red' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-800',
            'icon' => 'text-red-500',
            'dark' => 'bg-red-500'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100',
            'text' => 'text-indigo-800',
            'icon' => 'text-indigo-500',
            'dark' => 'bg-indigo-500'
        ]
    ];
    
    $colorClass = $colors[$color] ?? $colors['blue'];
    $isPositive = strpos($change, '+') !== false;
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="{{ $colorClass['bg'] }} rounded-md p-3">
                    <i class="{{ $icon }} h-6 w-6 {{ $colorClass['icon'] }}"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd>
                        @if($loading)
                            <div class="h-7 w-24 bg-gray-200 rounded animate-pulse"></div>
                        @else
                            <div class="text-2xl font-semibold text-gray-900">
                                {{ $value }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="{{ $colorClass['bg'] }} px-5 py-3">
        <div class="flex justify-between items-center">
            <div class="text-sm">
                @if($loading)
                    <div class="h-4 w-16 bg-gray-300 rounded animate-pulse"></div>
                @elseif($change)
                    <span class="font-medium {{ $colorClass['text'] }} flex items-center">
                        <i class="fas fa-arrow-{{ $isPositive ? 'up' : 'down' }} text-xs mr-1"></i>
                        {{ $change }}
                        <span class="text-gray-600 ml-1">from yesterday</span>
                    </span>
                @else
                    <span class="text-gray-600">Updated just now</span>
                @endif
            </div>
            <div class="text-xs">
                <button type="button" 
                        class="text-gray-500 hover:text-gray-700 focus:outline-none"
                        title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>