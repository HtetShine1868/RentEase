@extends('laundry-provider.layouts.provider')

@section('title', 'Review Details')
@section('subtitle', 'View customer review details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#174455]">Review Details</h3>
            <a href="{{ route('laundry-provider.reviews.index') }}" 
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Back to Reviews
            </a>
        </div>
        
        {{-- Content --}}
        <div class="p-6">
            {{-- Customer Info --}}
            <div class="mb-6 pb-6 border-b">
                <h4 class="text-sm font-medium text-gray-500 mb-3">Customer Information</h4>
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-[#174455] rounded-full flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($review->user->name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-medium text-gray-900">{{ $review->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $review->user->email }}</p>
                        @if($review->user->phone)
                            <p class="text-sm text-gray-500">{{ $review->user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Review Details --}}
            <div class="mb-6 pb-6 border-b">
                <h4 class="text-sm font-medium text-gray-500 mb-3">Review Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Date</p>
                        <p class="font-medium">{{ $review->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Order Type</p>
                        <p class="font-medium">{{ $review->order_type }}</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Overall Rating</p>
                    <div class="flex items-center">
                        <div class="flex text-2xl">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($review->overall_rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - 0.5 <= $review->overall_rating)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-3 text-xl font-bold text-[#174455]">{{ number_format($review->overall_rating, 1) }}</span>
                        <span class="ml-2 text-sm text-gray-500">/ 5.0</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Quality</p>
                        <p class="text-lg font-bold text-green-600">{{ $review->quality_rating }}/5</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Delivery</p>
                        <p class="text-lg font-bold text-blue-600">{{ $review->delivery_rating }}/5</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Value</p>
                        <p class="text-lg font-bold text-purple-600">{{ $review->value_rating }}/5</p>
                    </div>
                </div>
                
                @if($review->comment)
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 mb-2">Customer Comment</p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700">{{ $review->comment }}</p>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Order Info --}}
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3">Related Order</h4>
                <p class="text-gray-600">Order ID: <span class="font-medium">#{{ $review->order_id }}</span></p>
                <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                    View Order Details <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection