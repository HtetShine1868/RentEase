@props([
    'item' => null,
    'showActions' => true,
    'compact' => false
])

@php
    $item = $item ?? [
        'id' => 1,
        'name' => 'Butter Chicken',
        'description' => 'Rich creamy curry with tandoori chicken',
        'price' => 320,
        'category' => 'Non-Veg',
        'category_color' => 'red',
        'meal_types' => ['lunch', 'dinner'],
        'status' => 'active',
        'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop',
        'preparation_time' => 25,
        'is_featured' => true
    ];
    
    $mealTypeIcons = [
        'breakfast' => ['icon' => 'fa-sun', 'color' => 'yellow'],
        'lunch' => ['icon' => 'fa-utensils', 'color' => 'orange'],
        'dinner' => ['icon' => 'fa-moon', 'color' => 'blue']
    ];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 overflow-hidden">
    <!-- Featured Badge -->
    @if($item['is_featured'] ?? false)
    <div class="absolute top-2 left-2 z-10">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <i class="fas fa-star mr-1 text-xs"></i> Featured
        </span>
    </div>
    @endif
    
    <!-- Image -->
    <div class="relative h-40 bg-gray-200 overflow-hidden">
        <img src="{{ $item['image_url'] }}" 
             alt="{{ $item['name'] }}" 
             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        
        <!-- Quick Status Toggle -->
        @if($showActions)
        <div class="absolute top-2 right-2">
            <button type="button" 
                    class="h-6 w-6 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:text-gray-900"
                    title="Toggle Status">
                <i class="fas fa-eye{{ ($item['status'] ?? 'active') === 'active' ? '' : '-slash' }} text-xs"></i>
            </button>
        </div>
        @endif
    </div>
    
    <!-- Content -->
    <div class="p-4">
        <!-- Header -->
        <div class="flex justify-between items-start">
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-900 truncate">
                    {{ $item['name'] }}
                </h4>
                @if(!$compact && ($item['description'] ?? null))
                <p class="mt-1 text-xs text-gray-500 line-clamp-2">
                    {{ $item['description'] }}
                </p>
                @endif
            </div>
            
            <div class="ml-2 flex-shrink-0">
                <span class="text-sm font-bold text-gray-900">
                    MMK{{ $item['price'] }}
                </span>
            </div>
        </div>
        
        <!-- Tags & Info -->
        <div class="mt-3">
            <div class="flex flex-wrap gap-1">
                <!-- Category -->
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $item['category_color'] ?? 'gray' }}-100 text-{{ $item['category_color'] ?? 'gray' }}-800">
                    {{ $item['category'] }}
                </span>
                
                <!-- Meal Types -->
                @foreach($item['meal_types'] ?? [] as $mealType)
                @if(isset($mealTypeIcons[$mealType]))
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                      title="{{ ucfirst($mealType) }}">
                    <i class="fas {{ $mealTypeIcons[$mealType]['icon'] }} mr-1 text-{{ $mealTypeIcons[$mealType]['color'] }}-500"></i>
                    {{ strtoupper(substr($mealType, 0, 1)) }}
                </span>
                @endif
                @endforeach
                
                <!-- Preparation Time -->
                @if($item['preparation_time'] ?? false)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-clock mr-1 text-xs"></i>
                    {{ $item['preparation_time'] }}min
                </span>
                @endif
            </div>
        </div>
        
        <!-- Status & Actions -->
        <div class="mt-4 flex items-center justify-between">
            <!-- Status -->
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ ($item['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                <i class="fas fa-circle text-xs mr-1 {{ ($item['status'] ?? 'active') === 'active' ? 'text-green-500' : 'text-gray-500' }}"></i>
                {{ ucfirst($item['status'] ?? 'active') }}
            </span>
            
            <!-- Actions -->
            @if($showActions)
            <div class="flex space-x-2">
                <a href="{{ route('food-provider.menu.items.edit', $item['id']) }}" 
                   class="text-gray-400 hover:text-indigo-600"
                   title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <button
                    type="button"
                    class="text-gray-400 hover:text-red-600 delete-item-btn"
                    data-id="{{ $item['id'] }}"
                    title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
                <button type="button" 
                    class="text-gray-400 hover:text-green-600"
                    title="Duplicate">
                    <i class="fas fa-copy"></i>
                </button>

            </div>
            @endif
        </div>
        
        <!-- Earnings Info -->
        @if(!$compact)
        <div class="mt-3 pt-3 border-t border-gray-100">
            <div class="flex justify-between text-xs text-gray-500">
                <span>Your earnings:</span>
                <span class="font-medium text-green-600">
                    MMK{{ number_format($item['price'] * 0.88, 2) }}
                </span>
            </div>
        </div>
        @endif
    </div>
</div>