@extends('layouts.food-provider')

@section('title', 'Order Details')

@section('header', 'Order #ORD-2023-001')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Order Status Banner -->
    <div class="mb-6">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-utensils text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Order #ORD-2023-001</h2>
                            <div class="mt-1 flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1 text-xs"></i>
                                    Preparing
                                </span>
                                <span class="text-sm text-gray-500">
                                    Placed on {{ now()->format('F d, Y') }} at 10:30 AM
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-500">Order Type:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Pay-per-eat
                            </span>
                            <span class="text-lg font-bold text-gray-900">₹450</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Timeline -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Status Timeline</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Track the progress of this order
                    </p>
                </div>
                <div class="p-6">
                    @include('components.food-provider.order-timeline', [
                        'currentStatus' => 'preparing'
                    ])
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        2 items in this order
                    </p>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-my-6 divide-y divide-gray-200">
                            <!-- Item 1 -->
                            <li class="py-6 flex">
                                <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop" 
                                         alt="Butter Chicken" 
                                         class="w-full h-full object-center object-cover">
                                </div>
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>Butter Chicken</h3>
                                            <p class="ml-4">₹320</p>
                                        </div>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">
                                                Non-Veg
                                            </span>
                                            <span class="text-sm text-gray-500">Rich creamy curry with tandoori chicken</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-gray-500">Quantity: 1</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">Preparation Time: 25-30 mins</p>
                                        <div class="flex">
                                            <p class="font-medium">Total: ₹320</p>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Item 2 -->
                            <li class="py-6 flex">
                                <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1594041680534-e8c8cdebd659?w=100&h=100&fit=crop" 
                                         alt="Garlic Naan" 
                                         class="w-full h-full object-center object-cover">
                                </div>
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>Garlic Naan</h3>
                                            <p class="ml-4">₹80</p>
                                        </div>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                                                Vegetarian
                                            </span>
                                            <span class="text-sm text-gray-500">Freshly baked garlic flavored bread</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-gray-500">Quantity: 2</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">Preparation Time: 10-15 mins</p>
                                        <div class="flex">
                                            <p class="font-medium">Total: ₹160</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Customer Notes & Special Instructions -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Notes & Instructions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Allergy Alert -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Allergy Alert:</strong> Customer is allergic to nuts. Please ensure no nut products are used in preparation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Special Instructions</h4>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm text-gray-700">
                                "Please make the butter chicken less spicy and add extra gravy if possible. For garlic naan, make it crispy."
                            </p>
                        </div>
                    </div>

                    <!-- Delivery Instructions -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Delivery Instructions</h4>
                        <div class="bg-blue-50 p-4 rounded-md">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>Leave at door:</strong> Please leave the order at the door and ring the bell twice.
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Customer prefers contactless delivery
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Customer Info & Actions -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-800 font-medium text-lg">JD</span>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">John Doe</h5>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>4.8 • Regular Customer</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone text-gray-400 mr-2 w-5"></i>
                            <span>+91 9876543210</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope text-gray-400 mr-2 w-5"></i>
                            <span>john.doe@example.com</span>
                        </div>
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2 w-5 mt-0.5"></i>
                            <span>123 Main Street, Apartment 4B, Downtown, City 12345</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-pin text-green-400 mr-2"></i>
                                <span class="text-green-600 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Within 2km radius • Delivery: 10-15 mins
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Butter Chicken (1)</span>
                            <span class="font-medium">₹320</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Garlic Naan (2)</span>
                            <span class="font-medium">₹160</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="font-medium">₹50</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax (5%)</span>
                            <span class="font-medium">₹25</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-base font-medium">
                                <span>Total</span>
                                <span>₹555</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-1">
                                <span>Platform Commission (12%)</span>
                                <span class="text-red-600">-₹66.60</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-green-700 mt-2">
                                <span>Your Earnings</span>
                                <span>₹488.40</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Payment Method</span>
                            <span class="font-medium text-gray-700">
                                <i class="fas fa-credit-card mr-1"></i>
                                Credit Card • Paid
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Update Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <button type="button" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-check mr-2"></i> Accept Order
                        </button>
                        
                        <button type="button" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-utensils mr-2"></i> Start Preparing
                        </button>
                        
                        <button type="button" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-shipping-fast mr-2"></i> Out for Delivery
                        </button>
                        
                        <button type="button" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-flag-checkered mr-2"></i> Mark as Delivered
                        </button>
                        
                        <div class="pt-3 border-t">
                            <button type="button" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md shadow-sm text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Report Delay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delivery Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Estimated Prep Time</span>
                            <span class="font-medium">25-30 mins</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Time</span>
                            <span class="font-medium">10-15 mins</span>
                        </div>
                        <div class="pt-3 border-t">
                            <div class="flex justify-between text-sm font-medium">
                                <span>Total Estimated Time</span>
                                <span class="text-blue-600">40-45 mins</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                Order should be delivered by {{ now()->addMinutes(45)->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Delivery Address</span>
                            <button type="button" 
                                    class="text-indigo-600 hover:text-indigo-500 text-xs">
                                <i class="fas fa-directions mr-1"></i> Get Directions
                            </button>
                        </div>
                        <div class="mt-2 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-700">
                                123 Main Street, Apartment 4B<br>
                                Downtown, City 12345
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                        <button type="button" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-file-invoice mr-2"></i> Invoice
                        </button>
                        <button type="button" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-comment-alt mr-2"></i> Message
                        </button>
                        <button type="button" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-phone-alt mr-2"></i> Call
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Previous Orders from Same Customer -->
    <div class="mt-8 bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Previous Orders from John Doe</h3>
            <p class="mt-1 text-sm text-gray-500">
                Customer's order history
            </p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ now()->subDays(2)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #ORD-2023-045
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                3 items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹680
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Delivered
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ now()->subDays(5)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #ORD-2023-038
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                2 items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹450
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Delivered
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ now()->subDays(10)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #ORD-2023-029
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                1 item
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹320
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Delivered
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection