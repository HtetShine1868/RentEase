@extends('layouts.food-provider')

@section('title', 'Menu Management')

@section('header', 'Menu Items')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Menu Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage your restaurant menu items and categories
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @if(Route::has('food-provider.menu.categories.index'))
                <a href="{{ route('food-provider.menu.categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-tags mr-2"></i>
                    Manage Categories
                </a>
            @endif
            <a href="{{ route('food-provider.menu.items.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Add New Item
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-2">
                        <i class="fas fa-utensils text-indigo-600 h-5 w-5"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Items
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $totalItems ?? 0 }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-2">
                        <i class="fas fa-check-circle text-green-600 h-5 w-5"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Active Items
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $activeItems ?? 0 }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-2">
                        <i class="fas fa-star text-yellow-600 h-5 w-5"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Most Popular
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $mostPopularItem ?? 'N/A' }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-2">
                        <i class="fas fa-rupee-sign text-blue-600 h-5 w-5"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. Price
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            MMK{{ number_format($averagePrice ?? 0, 2) }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('food-provider.menu.items.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex-1">
                    <label for="search" class="sr-only">Search items</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Search menu items by name or description...">
                        @if(request('search'))
                            <a href="{{ route('food-provider.menu.items.index') }}" 
                               class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="category" 
                            name="category" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Categories</option>
                        <option value="vegetarian" {{ request('category') == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                        <option value="non-vegetarian" {{ request('category') == 'non-vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
                    </select>
                    
                    @if(isset($mealTypes) && $mealTypes->isNotEmpty())
                    <select id="meal_type" 
                            name="meal_type" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Meal Types</option>
                        @foreach($mealTypes as $mealType)
                            <option value="{{ $mealType->id }}" {{ request('meal_type') == $mealType->id ? 'selected' : '' }}>
                                {{ $mealType->name }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                    
                    <select id="status" 
                            name="status" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    
                    <a href="{{ route('food-provider.menu.items.index') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Menu Items Grid/Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if(isset($menuItems) && $menuItems->isNotEmpty())
            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Meal Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($menuItems as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-md object-cover" 
                                             src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop' }}" 
                                             alt="{{ $item->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            {{ Str::limit($item->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isVegetarian = $item->dietary_tags && in_array('vegetarian', json_decode($item->dietary_tags, true) ?? []);
                                    $categoryColor = $isVegetarian ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $categoryText = $isVegetarian ? 'Vegetarian' : 'Non-Veg';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $categoryColor }}">
                                    {{ $categoryText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->mealType)
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                        {{ $item->mealType->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₹{{ number_format($item->base_price, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    You get: ₹{{ number_format($item->base_price - ($item->base_price * ($item->commission_rate / 100)), 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $item->is_available ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('food-provider.menu.items.edit', $item->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="showDeleteConfirmation({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form action="{{ route('food-provider.menu.items.toggle-status', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-gray-600 hover:text-gray-900"
                                                title="{{ $item->is_available ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas {{ $item->is_available ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($menuItems as $item)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <img class="h-16 w-16 rounded-md object-cover" 
                                     src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop' }}" 
                                     alt="{{ $item->name }}">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item->name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($item->description, 30) }}</p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        @php
                                            $isVegetarian = $item->dietary_tags && in_array('vegetarian', json_decode($item->dietary_tags, true) ?? []);
                                        @endphp
                                        <span class="px-2 py-1 text-xs {{ $isVegetarian ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded">
                                            {{ $isVegetarian ? 'Veg' : 'Non-Veg' }}
                                        </span>
                                        @if($item->mealType)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                {{ $item->mealType->name }}
                                            </span>
                                        @endif
                                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($item->base_price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 text-xs {{ $item->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded">
                                    {{ $item->is_available ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="flex space-x-2">
                                    <a href="{{ route('food-provider.menu.items.edit', $item->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="showDeleteConfirmation({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($menuItems->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($menuItems->previousPageUrl())
                            <a href="{{ $menuItems->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        @endif
                        
                        @if($menuItems->nextPageUrl())
                            <a href="{{ $menuItems->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $menuItems->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $menuItems->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $menuItems->total() }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            {{ $menuItems->links('vendor.pagination.tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-utensils text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No menu items yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first menu item to attract customers.</p>
                <div class="mt-6">
                    <a href="{{ route('food-provider.menu.items.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Your First Menu Item
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<x-food-provider.confirmation-modal
    id="delete-menu-item-modal"
    title="Delete Menu Item"
    message="Are you sure you want to delete this menu item? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
    confirmColor="danger"
    icon="fas fa-trash"
    iconColor="text-red-400">
</x-food-provider.confirmation-modal>

@push('scripts')
<script>
    function showDeleteConfirmation(itemId, itemName) {
        showConfirmation({
            id: 'delete-menu-item-modal',
            title: 'Delete Menu Item',
            message: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
            confirmText: 'Delete',
            confirmColor: 'danger',
            onConfirm: () => deleteMenuItem(itemId)
        });
    }
    
    function deleteMenuItem(itemId) {
        // Create form and submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/food-provider/menu/items/${itemId}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = csrfToken;
        
        form.appendChild(methodField);
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
    }
    
    // Show toast notification if there's a success message
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
    
    function showToast(type, message) {
        // You can implement a toast notification here
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endpush
@endsection