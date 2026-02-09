@extends('layouts.food-provider')

@section('title', 'Edit Menu Item')

@section('header', 'Edit Menu Item')

@section('content')
@php
    // Use the correct variable name from controller
    $foodItem = $menuItem ?? null;
    $itemId = $foodItem->id ?? 'N/A';
    $updatedAt = $foodItem->updated_at ?? now();
    
    // Ensure mealTypes is set
    $mealTypes = $mealTypes ?? collect([]);
    
    // Dietary tags from controller
    $dietaryTags = $dietaryTags ?? [];
@endphp

<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg">
        <!-- Form Header -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Edit Menu Item
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Update your menu item details. Changes will be reflected immediately.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                        Item ID: #{{ $itemId }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                        Last updated: {{ $updatedAt->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('food-provider.menu.items.update', $foodItem->id) }}" method="POST" enctype="multipart/form-data" x-data="menuItemForm()">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6 space-y-8">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                    
                    <!-- Item Name & Meal Type -->
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
                                       value="{{ old('name', $foodItem->name) }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
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
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required>
                                    <option value="">Select Meal Type</option>
                                    @foreach($mealTypes as $mealType)
                                        <option value="{{ $mealType->id }}" 
                                                {{ old('meal_type_id', $foodItem->meal_type_id) == $mealType->id ? 'selected' : '' }}>
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
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      @input="updateCharCount">{{ old('description', $foodItem->description) }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Brief description about the item (optional).
                        </p>
                        <div class="mt-1 text-right text-xs text-gray-500">
                            <span id="char-count">0</span>/500 characters
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dietary Information -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dietary Information
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-center">
                                <input id="vegetarian" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="vegetarian" 
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                       {{ in_array('vegetarian', $dietaryTags) ? 'checked' : '' }}>
                                <label for="vegetarian" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-leaf mr-1"></i> Vegetarian
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="gluten_free" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="gluten_free" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       {{ in_array('gluten_free', $dietaryTags) ? 'checked' : '' }}>
                                <label for="gluten_free" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-wheat mr-1"></i> Gluten Free
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="spicy" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="spicy" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                       {{ in_array('spicy', $dietaryTags) ? 'checked' : '' }}>
                                <label for="spicy" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-pepper-hot mr-1"></i> Spicy
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="vegan" 
                                       name="dietary_tags[]" 
                                       type="checkbox" 
                                       value="vegan" 
                                       class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                       {{ in_array('vegan', $dietaryTags) ? 'checked' : '' }}>
                                <label for="vegan" class="ml-2 block text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800">
                                        <i class="fas fa-seedling mr-1"></i> Vegan
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Calories -->
                    <div class="mt-6">
                        <label for="calories" class="block text-sm font-medium text-gray-700">
                            Calories (optional)
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="calories" 
                                   id="calories" 
                                   min="0"
                                   step="1"
                                   value="{{ old('calories', $foodItem->calories) }}"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="e.g., 450">
                        </div>
                        @error('calories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing & Commission -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Pricing & Commission</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Base Price Input -->
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">
                                Base Price (৳) *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">৳</span>
                                </div>
                                <input type="number" 
                                       name="base_price" 
                                       id="base_price" 
                                       step="0.01"
                                       min="0.01" 
                                       x-model="basePrice"
                                       @input="calculateCommission"
                                       value="{{ old('base_price', $foodItem->base_price) }}"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md" 
                                       required>
                                @error('base_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your base price before commission
                            </p>
                        </div>

                        <!-- Commission Preview -->
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Commission Preview
                            </label>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Base Price:</span>
                                    <span class="font-medium" x-text="`৳${basePrice.toFixed(2)}`">৳{{ number_format($foodItem->base_price, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Commission ({{ $foodItem->commission_rate ?? 8.00 }}%):</span>
                                    <span class="text-red-600" x-text="`+৳${commission.toFixed(2)}`">+৳{{ number_format($foodItem->base_price * ($foodItem->commission_rate ?? 8.00) / 100, 2) }}</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-medium">
                                    <span class="text-gray-900">Customer Pays:</span>
                                    <span class="text-indigo-600" x-text="`৳${totalPrice.toFixed(2)}`">৳{{ number_format($foodItem->total_price, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">You'll Receive:</span>
                                    <span class="text-green-600" x-text="`৳${basePrice.toFixed(2)}`">৳{{ number_format($foodItem->base_price, 2) }}</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Commission rate: {{ $foodItem->commission_rate ?? 8.00 }}% for food items
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Availability -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Availability & Stock</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="is_available" class="block text-sm font-medium text-gray-700 mb-2">
                                Availability Status
                            </label>
                            <div class="mt-1">
                                <select id="is_available" 
                                        name="is_available" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="1" {{ $foodItem->is_available ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ !$foodItem->is_available ? 'selected' : '' }}>Not Available</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="daily_quantity" class="block text-sm font-medium text-gray-700">
                                Daily Quantity Limit
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="daily_quantity" 
                                       id="daily_quantity" 
                                       min="0"
                                       value="{{ old('daily_quantity', $foodItem->daily_quantity) }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Leave empty for unlimited">
                                <p class="mt-1 text-xs text-gray-500">
                                    Maximum number available per day. Leave empty for unlimited.
                                </p>
                            </div>
                            @error('daily_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Today's Sales -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-md border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-blue-900">Today's Sales</h5>
                                <p class="text-sm text-blue-700">
                                    Sold today: <span class="font-semibold">{{ $foodItem->sold_today ?? 0 }}</span>
                                    @if($foodItem->daily_quantity)
                                        / {{ $foodItem->daily_quantity }} available
                                    @else
                                        (unlimited)
                                    @endif
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Item Image</h4>
                    
                    <div class="flex flex-col items-center justify-center">
                        <div class="text-center">
                            <div class="relative">
                                <img :src="imagePreview || '{{ $foodItem->image_url ? asset($foodItem->image_url) : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=400&fit=crop' }}'" 
                                     alt="Menu item preview" 
                                     class="mx-auto h-48 w-48 object-cover rounded-lg shadow-lg">
                                @if($foodItem->image_url)
                                <button type="button" 
                                        @click="removeImage"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 focus:outline-none">
                                    <i class="fas fa-times h-4 w-4"></i>
                                </button>
                                @endif
                            </div>
                            <div class="mt-4">
                                <label for="image" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-upload mr-2"></i>
                                    {{ $foodItem->image_url ? 'Change Image' : 'Upload Image' }}
                                </label>
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       @change="updateImagePreview($event)"
                                       class="hidden">
                                <input type="hidden" name="remove_image" x-model="removeImageFlag">
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-500">
                                    Recommended: 800x800px, JPG or PNG, max 2MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Statistics -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Item Performance</h4>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Sales Statistics</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="text-xs text-gray-500">Total Sold</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $foodItem->sold_today ?? 0 }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Base Price</div>
                                <div class="text-lg font-semibold text-gray-900">৳{{ number_format($foodItem->base_price, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Total Price</div>
                                <div class="text-lg font-semibold text-gray-900">৳{{ number_format($foodItem->total_price, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Your Earnings</div>
                                <div class="text-lg font-semibold text-green-600">
                                    ৳{{ number_format($foodItem->base_price * ($foodItem->sold_today ?? 0), 2) }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Popularity -->
                        @if($foodItem->daily_quantity && $foodItem->daily_quantity > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="text-xs text-gray-500 mb-2">Popularity Trend (Today)</div>
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, (($foodItem->sold_today ?? 0) / max(1, $foodItem->daily_quantity)) * 100) }}%"></div>
                                </div>
                                <span class="ml-2 text-xs font-medium text-gray-700">
                                    {{ number_format(min(100, (($foodItem->sold_today ?? 0) / max(1, $foodItem->daily_quantity)) * 100), 1) }}%
                                </span>
                            </div>
                        </div>
                        @endif
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
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 delete-btn"
                        data-id="{{ $foodItem->id }}"
                        onclick="showDeleteConfirmation({{ $foodItem->id }})">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                <button type="submit"
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Update Item
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<x-food-provider.confirmation-modal
    id="delete-item-modal"
    title="Delete Menu Item"
    message="Are you sure you want to delete this menu item? This action cannot be undone and will remove all associated data."
    confirmText="Delete Item"
    cancelText="Cancel"
    confirmColor="danger"
    icon="fas fa-trash"
    iconColor="text-red-400">
</x-food-provider.confirmation-modal>

@push('scripts')
<script>
    function menuItemForm() {
        return {
            itemName: '{{ $foodItem ? addslashes($foodItem->name) : '' }}',
            basePrice: {{ $foodItem ? $foodItem->base_price : 0 }},
            commission: {{ $foodItem ? $foodItem->base_price * ($foodItem->commission_rate ?? 8.00) / 100 : 0 }},
            totalPrice: {{ $foodItem ? $foodItem->total_price : 0 }},
            imagePreview: '',
            removeImageFlag: false,
            
            init() {
                if (this.basePrice > 0) {
                    this.calculateCommission();
                }
                this.updateCharCount();
            },
            
            calculateCommission() {
                const commissionRate = {{ $foodItem ? ($foodItem->commission_rate ?? 8.00) / 100 : 0.08 }};
                this.commission = this.basePrice * commissionRate;
                this.totalPrice = this.basePrice + this.commission;
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
                this.removeImageFlag = true;
                document.getElementById('image').value = '';
            },
            
            updateCharCount() {
                const textarea = document.getElementById('description');
                const count = textarea.value.length;
                document.getElementById('char-count').textContent = count;
                
                if (count > 500) {
                    document.getElementById('char-count').classList.add('text-red-600');
                } else {
                    document.getElementById('char-count').classList.remove('text-red-600');
                }
            }
        };
    }
    
    function showDeleteConfirmation(itemId) {
        // Show the modal (assuming you have a modal component)
        const modal = document.getElementById('delete-item-modal');
        if (modal) {
            modal.style.display = 'block';
        }
        
        // Set up delete action
        document.getElementById('confirm-delete').onclick = function() {
            deleteMenuItem(itemId);
        };
    }
    
    function deleteMenuItem(itemId) {
        fetch(`/food-provider/menu/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "{{ route('food-provider.menu.items.index') }}";
            } else {
                alert('Error deleting item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the item');
        });
    }
</script>
@endpush
@endsection