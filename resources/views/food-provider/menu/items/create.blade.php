@extends('layouts.food-provider')

@section('title', 'Add Menu Item')

@section('header', 'Add New Menu Item')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg">
        <!-- Form Header -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Menu Item Details
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Add a new item to your restaurant menu. Commission will be calculated automatically.
            </p>
        </div>

        <!-- Form -->
        <form action="#" method="POST" enctype="multipart/form-data" x-data="menuItemForm()">
            @csrf
            
            <div class="px-4 py-5 sm:p-6 space-y-8">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                    
                    <!-- Item Name & Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Item Name *
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       x-model="itemName"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="e.g., Butter Chicken"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Category *
                            </label>
                            <div class="mt-1">
                                <select id="category_id" 
                                        name="category_id" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required>
                                    <option value="">Select Category</option>
                                    <option value="1">Vegetarian</option>
                                    <option value="2">Non-Vegetarian</option>
                                    <option value="3">Vegan</option>
                                    <option value="4">Desserts</option>
                                    <option value="5">Beverages</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                      placeholder="Describe your menu item..."></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Brief description about the item (optional).
                        </p>
                    </div>

                    <!-- Dietary Information -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dietary Information
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-center">
                                <input id="is_vegetarian" 
                                       name="dietary_info[]" 
                                       type="checkbox" 
                                       value="vegetarian" 
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="is_vegetarian" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-leaf mr-1"></i> Vegetarian
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_gluten_free" 
                                       name="dietary_info[]" 
                                       type="checkbox" 
                                       value="gluten_free" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_gluten_free" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-wheat mr-1"></i> Gluten Free
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_spicy" 
                                       name="dietary_info[]" 
                                       type="checkbox" 
                                       value="spicy" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="is_spicy" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-pepper-hot mr-1"></i> Spicy
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Commission -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Pricing & Commission</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price Input -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Price (₹) *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                                <input type="number" 
                                       name="price" 
                                       id="price" 
                                       step="1"
                                       min="0"
                                       x-model="price"
                                       @input="calculateCommission"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md" 
                                       placeholder="0"
                                       required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Price shown to customers
                            </p>
                        </div>

                        <!-- Commission Preview -->
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Commission Preview
                            </label>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Customer Pays:</span>
                                    <span class="font-medium" x-text="`₹${price.toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Commission (12%):</span>
                                    <span class="text-red-600" x-text="`-₹${commission.toFixed(2)}`"></span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-medium">
                                    <span class="text-gray-900">You'll Receive:</span>
                                    <span class="text-green-600" x-text="`₹${earnings.toFixed(2)}`"></span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Commission varies: 12% for Pay-per-eat, 15% for Subscriptions
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Pricing -->
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input id="has_special_price" 
                                   name="has_special_price" 
                                   type="checkbox" 
                                   x-model="showSpecialPrice"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="has_special_price" class="ml-2 block text-sm text-gray-900">
                                Set special/discounted price
                            </label>
                        </div>
                        
                        <div x-show="showSpecialPrice" x-transition class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="special_price" class="block text-sm font-medium text-gray-700">
                                    Special Price (₹)
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₹</span>
                                    </div>
                                    <input type="number" 
                                           name="special_price" 
                                           id="special_price" 
                                           step="1"
                                           min="0"
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div>
                                <label for="special_price_end" class="block text-sm font-medium text-gray-700">
                                    Special Price Ends
                                </label>
                                <div class="mt-1">
                                    <input type="date" 
                                           name="special_price_end" 
                                           id="special_price_end" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Availability -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Meal Availability</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input id="breakfast" 
                                       name="meal_types[]" 
                                       type="checkbox" 
                                       value="breakfast" 
                                       class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 flex-1">
                                <label for="breakfast" class="font-medium text-gray-700">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-sun text-yellow-500 mr-2"></i> Breakfast
                                    </span>
                                </label>
                                <p class="text-sm text-gray-500">7:00 AM - 11:00 AM</p>
                            </div>
                        </div>
                        
                        <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input id="lunch" 
                                       name="meal_types[]" 
                                       type="checkbox" 
                                       value="lunch" 
                                       class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 flex-1">
                                <label for="lunch" class="font-medium text-gray-700">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-utensils text-orange-500 mr-2"></i> Lunch
                                    </span>
                                </label>
                                <p class="text-sm text-gray-500">12:00 PM - 3:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input id="dinner" 
                                       name="meal_types[]" 
                                       type="checkbox" 
                                       value="dinner" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 flex-1">
                                <label for="dinner" class="font-medium text-gray-700">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-moon text-blue-500 mr-2"></i> Dinner
                                    </span>
                                </label>
                                <p class="text-sm text-gray-500">7:00 PM - 11:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Item Image</h4>
                    
                    <div class="flex flex-col items-center justify-center">
                        <div x-show="!imagePreview" class="text-center">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload a high-quality image of your menu item
                                </p>
                                <label for="image" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-upload mr-2"></i>
                                    Choose Image
                                </label>
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       @change="updateImagePreview($event)"
                                       class="hidden">
                            </div>
                        </div>
                        
                        <div x-show="imagePreview" x-transition class="text-center">
                            <div class="relative">
                                <img :src="imagePreview" 
                                     alt="Menu item preview" 
                                     class="mx-auto h-48 w-48 object-cover rounded-lg shadow-lg">
                                <button type="button" 
                                        @click="removeImage"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 focus:outline-none">
                                    <i class="fas fa-times h-4 w-4"></i>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Click image to change
                            </p>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                Recommended: 800x800px, JPG or PNG, max 2MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Availability & Status -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Availability & Status</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Item Status
                            </label>
                            <div class="mt-1">
                                <select id="status" 
                                        name="status" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="active">Active (Visible to customers)</option>
                                    <option value="inactive">Inactive (Hidden from customers)</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">
                                Preparation Time (minutes)
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="preparation_time" 
                                       id="preparation_time" 
                                       min="5"
                                       max="60"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="e.g., 20"
                                       value="20">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Average time to prepare this item
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center">
                            <input id="is_featured" 
                                   name="is_featured" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                Feature this item on restaurant homepage
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('food-provider.menu.items.index') }}" 
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="button" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Save as Draft
                </button>
                <button type="submit" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save & Publish Item
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function menuItemForm() {
        return {
            itemName: '',
            price: 0,
            commission: 0,
            earnings: 0,
            imagePreview: '',
            showSpecialPrice: false,
            
            calculateCommission() {
                const commissionRate = 0.12; // 12% default
                this.commission = this.price * commissionRate;
                this.earnings = this.price - this.commission;
            },
            
            updateImagePreview(event) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            },
            
            removeImage() {
                this.imagePreview = '';
                document.getElementById('image').value = '';
            }
        };
    }
    
    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from item name
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                // You can add slug generation logic here if needed
            });
        }
    });
</script>
@endpush
@endsection