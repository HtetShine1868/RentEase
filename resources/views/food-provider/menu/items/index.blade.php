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
            <a href="{{ route('food-provider.menu.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-tags mr-2"></i>
                Manage Categories
            </a>
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
                            24
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
                            20
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
                            Butter Chicken
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
                            ₹280
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex-1">
                    <label for="search" class="sr-only">Search items</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Search menu items by name or description...">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="category" 
                            name="category" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Categories</option>
                        <option value="veg">Vegetarian</option>
                        <option value="non-veg">Non-Vegetarian</option>
                        <option value="vegan">Vegan</option>
                        <option value="dessert">Desserts</option>
                        <option value="beverage">Beverages</option>
                    </select>
                    
                    <select id="meal-type" 
                            name="meal-type" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Meal Types</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                    
                    <select id="status" 
                            name="status" 
                            class="block w-full md:w-auto pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items Grid/Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
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
                    @for($i = 1; $i <= 5; $i++)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" 
                                         src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop" 
                                         alt="Menu item">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        Butter Chicken
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Rich creamy curry with tandoori chicken
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Non-Veg
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-1">
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">L</span>
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">D</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">₹320</div>
                            <div class="text-xs text-gray-500">You get: ₹281.60</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('food-provider.menu.items.edit', $i) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="showDeleteConfirmation({ $i })"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button type="button" 
                                        class="text-gray-600 hover:text-gray-900"
                                        title="Toggle Status">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                                <button type="button" 
                                        class="text-green-600 hover:text-green-900"
                                        title="Duplicate">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden">
            <div class="divide-y divide-gray-200">
                @for($i = 1; $i <= 3; $i++)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <img class="h-16 w-16 rounded-md object-cover" 
                                 src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop" 
                                 alt="Menu item">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Butter Chicken</h4>
                                <p class="text-sm text-gray-500 mt-1">Rich creamy curry with tandoori chicken</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Non-Veg</span>
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">L</span>
                                    <span class="text-sm font-medium text-gray-900">₹320</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Active</span>
                            <div class="flex space-x-2">
                                <a href="{{ route('food-provider.menu.items.edit', $i) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="showDeleteConfirmation({ $i })">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">1</span>
                        to
                        <span class="font-medium">5</span>
                        of
                        <span class="font-medium">24</span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            1
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            2
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            3
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    @if(false) <!-- Replace with actual condition -->
    <x-food-provider.empty-state
        title="No menu items yet"
        description="Get started by creating your first menu item to attract customers."
        icon="fas fa-utensils"
        buttonText="Add Menu Item"
        buttonLink="{{ route('food-provider.menu.items.create') }}"
        secondaryButtonText="Import Menu"
        secondaryButtonLink="#">
    </x-food-provider.empty-state>
    @endif
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
    function showDeleteConfirmation(itemId) {
        showConfirmation({
            id: 'delete-menu-item-modal',
            title: 'Delete Menu Item',
            message: `Are you sure you want to delete menu item #${itemId}? This action cannot be undone.`,
            confirmText: 'Delete',
            onConfirm: `deleteMenuItem(${itemId})`
        });
    }
    
    function deleteMenuItem(itemId) {
        // Add your delete logic here
        console.log(`Deleting item ${itemId}`);
        // Show success toast
        showToast('success', 'Menu item deleted successfully');
    }
    
    function showToast(type, message) {
        // Toast notification logic
        console.log(`${type}: ${message}`);
    }
</script>
@endpush
@endsection