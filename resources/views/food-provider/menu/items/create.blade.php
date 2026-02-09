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
        <form action="{{ route('food-provider.menu.items.store') }}" method="POST" enctype="multipart/form-data" x-data="menuItemForm()" @submit="preventModalOpen()">
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
                                       value="{{ old('name') }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror" 
                                       placeholder="e.g., Butter Chicken"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="meal_type_id" class="block text-sm font-medium text-gray-700">
                                Meal Type *
                            </label>
                            <div class="mt-1">
                                <select id="meal_type_id" 
                                        name="meal_type_id" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('meal_type_id') border-red-300 @enderror"
                                        required>
                                    <option value="">Select Meal Type</option>
                                    @foreach($mealTypes as $mealType)
                                        <option value="{{ $mealType->id }}" {{ old('meal_type_id') == $mealType->id ? 'selected' : '' }}>
                                            {{ $mealType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('meal_type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 @enderror" 
                                      placeholder="Describe your menu item...">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing & Commission -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Pricing & Commission</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price Input -->
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">
                                Price (₹) *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                                <input type="number" 
                                       name="base_price" 
                                       id="base_price" 
                                       step="0.01"
                                       min="0,01"
                                       x-model="price"
                                       value="{{ old('base_price') }}"
                                       @input="calculateEarnings"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md @error('base_price') border-red-300 @enderror" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Price shown to customers
                            </p>
                            @error('base_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission Rate -->
                        <div>
                            <label for="commission_rate" class="block text-sm font-medium text-gray-700">
                                Commission Rate (%)
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" 
                                       name="commission_rate" 
                                       id="commission_rate" 
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       x-model="commissionRate"
                                       value="{{ old('commission_rate', '8.00') }}"
                                       @input="calculateEarnings"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md @error('commission_rate') border-red-300 @enderror" 
                                       placeholder="8.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Platform commission percentage (default: 8%)
                            </p>
                            @error('commission_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Earnings Preview -->
                        <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Earnings Breakdown
                            </label>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Customer Pays:</span>
                                    <span class="font-medium" x-text="`₹${totalPrice.toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Commission (<span x-text="`${commissionRate}%`"></span>):</span>
                                    <span class="text-red-600" x-text="`-₹${commissionAmount.toFixed(2)}`"></span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-medium">
                                    <span class="text-gray-900">You'll Receive:</span>
                                    <span class="text-green-600" x-text="`₹${providerEarnings.toFixed(2)}`"></span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Example: If price is ₹70 with 8% commission, you receive ₹64.40
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Quantity -->
                    <div class="mt-6">
                        <label for="daily_quantity" class="block text-sm font-medium text-gray-700">
                            Daily Quantity Limit (optional)
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="daily_quantity" 
                                   id="daily_quantity" 
                                   value="{{ old('daily_quantity') }}"
                                   min="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('daily_quantity') border-red-300 @enderror" 
                                   placeholder="Leave empty for unlimited">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Maximum number available per day
                        </p>
                        @error('daily_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dietary Information & Nutrition -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Dietary Information & Nutrition</h4>
                    
                    <!-- Dietary Tags -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dietary Information
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-center">
                                <input id="is_vegetarian" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="vegetarian" 
                                       {{ in_array('vegetarian', old('dietary_tags', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="is_vegetarian" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-leaf mr-1"></i> Vegetarian
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_vegan" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="vegan" 
                                       {{ in_array('vegan', old('dietary_tags', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="is_vegan" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-seedling mr-1"></i> Vegan
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_gluten_free" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="gluten-free" 
                                       {{ in_array('gluten-free', old('dietary_tags', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_gluten_free" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-wheat mr-1"></i> Gluten Free
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_spicy" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="spicy" 
                                       {{ in_array('spicy', old('dietary_tags', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="is_spicy" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-pepper-hot mr-1"></i> Spicy
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Calories -->
                    <div>
                        <label for="calories" class="block text-sm font-medium text-gray-700">
                            Calories (optional)
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="calories" 
                                   id="calories" 
                                   value="{{ old('calories') }}"
                                   min="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('calories') border-red-300 @enderror" 
                                   placeholder="e.g., 350">
                        </div>
                        @error('calories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                       class="hidden @error('image') border-red-300 @enderror">
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                Click "Choose Image" to change
                            </p>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                Recommended: 800x800px, JPG, PNG, or GIF, max 2MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Availability & Status</h4>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="is_available" class="block text-sm font-medium text-gray-700">
                                Item Status *
                            </label>
                            <div class="mt-1">
                                <select id="is_available" 
                                        name="is_available" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('is_available') border-red-300 @enderror"
                                        required>
                                    <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>Active (Visible to customers)</option>
                                    <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Inactive (Hidden from customers)</option>
                                </select>
                            </div>
                            @error('is_available')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
            price: parseFloat("{{ old('base_price', 70) }}") || 70,
            commissionRate: parseFloat("{{ old('commission_rate', 8.00) }}") || 8.00,
            commissionAmount: 0,
            totalPrice: 0,
            providerEarnings: 0,
            imagePreview: '',
            
            calculateEarnings() {
                // Calculate commission amount
                this.commissionAmount = (this.price * this.commissionRate) / 100;
                // Calculate total price customer pays
                this.totalPrice = this.price;
                // Calculate provider earnings (price minus commission)
                this.providerEarnings = this.price - this.commissionAmount;
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
            },
            
            preventModalOpen() {
                // Prevent any modal from opening on form submit
                document.body.classList.remove('modal-open');
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                });
            },
            
            init() {
                // Initialize earnings calculation
                this.calculateEarnings();
                
                // Listen for input changes
                const basePriceInput = document.getElementById('base_price');
                const commissionRateInput = document.getElementById('commission_rate');
                
                if (basePriceInput) {
                    basePriceInput.addEventListener('input', () => {
                        this.price = parseFloat(basePriceInput.value) || 0;
                        this.calculateEarnings();
                    });
                }
                
                if (commissionRateInput) {
                    commissionRateInput.addEventListener('input', () => {
                        this.commissionRate = parseFloat(commissionRateInput.value) || 0;
                        this.calculateEarnings();
                    });
                }
                
                // Hide any modals on page load
                setTimeout(() => {
                    const modals = document.querySelectorAll('[id*="modal"], [id*="Modal"]');
                    modals.forEach(modal => {
                        if (modal.style.display !== 'none') {
                            modal.style.display = 'none';
                        }
                    });
                }, 100);
            }
        };
    }
    
    // Initialize when Alpine is ready
    document.addEventListener('alpine:init', () => {
        Alpine.data('menuItemForm', menuItemForm);
    });
    
    // Additional script to prevent modal issues
    document.addEventListener('DOMContentLoaded', function() {
        // Close any open modals
        const closeModalButtons = document.querySelectorAll('[data-dismiss="modal"], .modal-close');
        closeModalButtons.forEach(button => {
            button.click();
        });
        
        // Hide modals by class
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
        
        // Remove modal backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            backdrop.remove();
        });
        
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '0';
    });
</script>
@endpush
@endsection