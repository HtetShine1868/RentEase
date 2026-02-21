@extends('laundry-provider.layouts.provider')

@section('title', 'Add New Item')
@section('subtitle', 'Create a new laundry item')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('laundry-provider.items.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                {{-- Item Name --}}
                <div>
                    <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455] @error('item_name') border-red-500 @enderror"
                           placeholder="e.g., T-Shirt, Jeans, Bedsheet">
                    @error('item_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Item Type --}}
                <div>
                    <label for="item_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Item Type <span class="text-red-500">*</span>
                    </label>
                    <select name="item_type" id="item_type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455] @error('item_type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        @foreach($itemTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('item_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('item_type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455] @error('description') border-red-500 @enderror"
                              placeholder="Optional description or notes">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Pricing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Base Price ($) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" name="base_price" id="base_price" value="{{ old('base_price') }}" required
                                   step="0.01" min="0"
                                   class="pl-8 w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455] @error('base_price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('base_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="rush_surcharge_percent" class="block text-sm font-medium text-gray-700 mb-1">
                            Rush Surcharge (%)
                        </label>
                        <div class="relative">
                            <input type="number" name="rush_surcharge_percent" id="rush_surcharge_percent" 
                                   value="{{ old('rush_surcharge_percent', 30) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455] @error('rush_surcharge_percent') border-red-500 @enderror"
                                   placeholder="30">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        @error('rush_surcharge_percent')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                {{-- Active Status --}}
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#174455] shadow-sm focus:ring-[#174455]">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active (item will be available for orders)
                    </label>
                </div>
                
                {{-- Calculated Total --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Calculated Total Price:</p>
                    <p class="text-2xl font-bold text-[#174455]" id="total-price-preview">৳0.00</p>
                    <p class="text-xs text-gray-500 mt-1">Includes base price + commission</p>
                </div>
                
                {{-- Submit Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t">
                    <button type="submit" class="bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                        <i class="fas fa-save mr-2"></i> Save Item
                    </button>
                    <a href="{{ route('laundry-provider.items.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Live price calculation preview
    document.getElementById('base_price').addEventListener('input', updatePricePreview);
    document.getElementById('rush_surcharge_percent').addEventListener('input', updatePricePreview);
    
    function updatePricePreview() {
        const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
        const commissionRate = 10; // Default commission rate from config
        
        const total = basePrice + (basePrice * commissionRate / 100);
        
        document.getElementById('total-price-preview').textContent = '৳' + total.toFixed(2);
    }
    
    // Initial calculation
    updatePricePreview();
</script>
@endpush