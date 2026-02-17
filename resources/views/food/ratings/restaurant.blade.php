@extends('dashboard')

@section('title', $provider->business_name . ' - Reviews')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Restaurant Header -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $provider->business_name }}</h2>
                    <p class="text-indigo-100 text-sm mt-1">Customer Reviews & Ratings</p>
                </div>
                <a href="{{ route('food.index') }}" class="text-white hover:text-indigo-100">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Restaurants
                </a>
            </div>
        </div>

        <!-- Rating Summary -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Overall Rating -->
                <div class="text-center md:border-r border-gray-200">
                    <div class="text-5xl font-bold text-indigo-600">{{ number_format($stats['average'], 1) }}</div>
                    <div class="flex justify-center mt-2 text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($stats['average']))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $stats['average'])
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="mt-1 text-sm text-gray-500">{{ $stats['total'] }} reviews</div>
                </div>

                <!-- Rating Breakdown -->
                <div class="md:col-span-3">
                    <!-- Quality Rating -->
                    <div class="flex items-center mb-2">
                        <span class="w-24 text-sm text-gray-600">Food Quality</span>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($stats['quality_avg'] / 5) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($stats['quality_avg'], 1) }}/5</span>
                    </div>

                    <!-- Delivery Rating -->
                    <div class="flex items-center mb-2">
                        <span class="w-24 text-sm text-gray-600">Delivery</span>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($stats['delivery_avg'] / 5) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($stats['delivery_avg'], 1) }}/5</span>
                    </div>

                    <!-- Value Rating -->
                    <div class="flex items-center">
                        <span class="w-24 text-sm text-gray-600">Value</span>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($stats['value_avg'] / 5) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($stats['value_avg'], 1) }}/5</span>
                    </div>
                </div>
            </div>

            <!-- Star Breakdown -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Rating Distribution</h4>
                @foreach([5,4,3,2,1] as $star)
                    @php
                        $count = $stats['breakdown'][$star] ?? 0;
                        $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                    @endphp
                    <div class="flex items-center mb-2">
                        <span class="text-sm text-gray-600 w-12">{{ $star }} star</span>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 w-12">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Reviews</h3>
        </div>

        <div class="p-6">
            @if($ratings->count() > 0)
                <div class="space-y-6">
                    @foreach($ratings as $rating)
                        <div class="border-b border-gray-100 last:border-0 pb-6 last:pb-0">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        @if($rating->user->avatar_url)
                                            <img src="{{ Storage::url($rating->user->avatar_url) }}" 
                                                 alt="{{ $rating->user->name }}"
                                                 class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <span class="text-indigo-600 font-semibold">
                                                {{ substr($rating->user->name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $rating->user->name }}</h4>
                                        <div class="flex items-center mt-1">
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $rating->overall_rating)
                                                        <i class="fas fa-star text-xs"></i>
                                                    @else
                                                        <i class="far fa-star text-xs"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">
                                                {{ $rating->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Order #{{ $rating->order->order_reference ?? 'N/A' }}
                                </div>
                            </div>

                            <!-- Detailed Ratings -->
                            <div class="mt-3 grid grid-cols-3 gap-2 max-w-md">
                                <div class="text-xs">
                                    <span class="text-gray-500">Quality:</span>
                                    <span class="ml-1 font-medium">{{ $rating->quality_rating }}/5</span>
                                </div>
                                <div class="text-xs">
                                    <span class="text-gray-500">Delivery:</span>
                                    <span class="ml-1 font-medium">{{ $rating->delivery_rating }}/5</span>
                                </div>
                                <div class="text-xs">
                                    <span class="text-gray-500">Value:</span>
                                    <span class="ml-1 font-medium">{{ $rating->value_rating }}/5</span>
                                </div>
                            </div>

                            @if($rating->comment)
                                <p class="mt-3 text-sm text-gray-700">{{ $rating->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($ratings->hasPages())
                    <div class="mt-6">
                        {{ $ratings->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-star text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No reviews yet</h3>
                    <p class="mt-2 text-gray-500">Be the first to review this restaurant!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection