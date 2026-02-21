@extends('laundry-provider.layouts.provider')

@section('title', $item->item_name)
@section('subtitle', 'Item Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#174455]">Item Information</h3>
            <div class="flex gap-2">
                <a href="{{ route('laundry-provider.items.edit', $item->id) }}" 
                   class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('laundry-provider.items.index') }}" 
                   class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Item Name</p>
                        <p class="text-lg font-medium">{{ $item->item_name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Item Type</p>
                        <p class="text-lg font-medium">
                            @php
                                $types = ['CLOTHING' => 'Clothing', 'BEDDING' => 'Bedding', 'CURTAIN' => 'Curtain', 'OTHER' => 'Other'];
                            @endphp
                            {{ $types[$item->item_type] ?? $item->item_type }}
                        </p>
                    </div>
                    
                    @if($item->description)
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-gray-700">{{ $item->description }}</p>
                    </div>
                    @endif
                </div>
                
                {{-- Right Column --}}
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Base Price</p>
                        <p class="text-2xl font-bold text-[#174455]">৳{{ number_format($item->base_price, 2) }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Rush Surcharge</p>
                        <p class="text-lg font-medium">{{ $item->rush_surcharge_percent }}%</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Commission Rate</p>
                        <p class="text-lg font-medium">{{ $item->commission_rate }}%</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Total Price (with commission)</p>
                        <p class="text-2xl font-bold text-green-600">৳{{ number_format($item->total_price, 2) }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Status</p>
                        @if($item->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">Inactive</span>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="text-gray-700">{{ $item->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Last Updated</p>
                        <p class="text-gray-700">{{ $item->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Order History Preview --}}
            @if($item->orderItems()->count() > 0)
            <div class="mt-8 pt-6 border-t">
                <h4 class="text-lg font-semibold text-[#174455] mb-4">Order History</h4>
                <p class="text-gray-600">This item has been used in {{ $item->orderItems()->count() }} orders.</p>
                <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block">View all orders with this item →</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection