<aside class="flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white">
    <!-- Logo -->
    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-indigo-700">
        <a href="{{ route('food-provider.dashboard') }}" class="flex items-center space-x-3">
            <i class="fas fa-utensils text-white text-xl"></i>
            <span class="text-white font-bold text-lg">RMS Food</span>
        </a>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <div class="px-3 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('food-provider.dashboard') }}" 
               class="{{ request()->routeIs('food-provider.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-chart-line mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.dashboard') ? 'text-indigo-500' : '' }}"></i>
                Dashboard
            </a>
            
            <!-- Restaurant Profile -->
            <a href="{{ route('food-provider.profile.index') }}" 
               class="{{ request()->routeIs('food-provider.profile.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-store mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.profile.*') ? 'text-indigo-500' : '' }}"></i>
                Restaurant Profile
            </a>
            
            <!-- Menu Management -->
             <a href="{{ route('food-provider.menu.items.index') }}" 
               class="{{ request()->routeIs('food-provider.menu.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-list-alt mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.menu.*') ? 'text-indigo-500' : '' }}"></i>
                Menu Management
            </a>
            
            <!-- Orders -->
            <a href="{{ route('food-provider.orders.index') }}" 
               class="{{ request()->routeIs('food-provider.orders.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-shopping-cart mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.orders.*') ? 'text-indigo-500' : '' }}"></i>
                Orders
                <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-red-100 text-red-800" id="pending-orders-count">
                    0
                </span>
            </a>
            
            <!-- Subscriptions -->
            <a href="{{ route('food-provider.subscriptions.index') }}" 
               class="{{ request()->routeIs('food-provider.subscriptions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-calendar-check mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.subscriptions.*') ? 'text-indigo-500' : '' }}"></i>
                Subscriptions
            </a>
            
            <!-- Reviews -->
            <a href="{{ route('food-provider.reviews.index') }}" 
               class="{{ request()->routeIs('food-provider.reviews.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <i class="fas fa-star mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('food-provider.reviews.*') ? 'text-indigo-500' : '' }}"></i>
                Reviews
            </a>

        </div>
        
        <div class="mt-auto px-3 py-4">
            <div class="border-t border-gray-200 pt-4">
                <p class="text-xs text-gray-500 px-2">
                    © {{ date('Y') }} RMS Food Provider
                </p>
                <p class="text-xs text-gray-400 px-2 mt-1">
                    v1.0.0
                </p>
            </div>
        </div>
    </nav>
</aside>

@push('scripts')
<script>
    // Update badge counts (placeholder - will be replaced with Livewire)
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate loading data
        setTimeout(() => {
            document.getElementById('pending-orders-count').textContent = '3';
            document.getElementById('unread-notifications-count').textContent = '5';
        }, 500);
    });
</script>
@endpush