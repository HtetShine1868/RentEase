@extends('dashboard')

@section('title', $provider->business_name)
@section('subtitle', 'Laundry Service Provider')

@section('content')
<div class="space-y-6">
    {{-- Provider Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    @if($provider->avatar_url)
                        <img src="{{ Storage::url($provider->avatar_url) }}" 
                             alt="{{ $provider->business_name }}"
                             class="w-20 h-20 rounded-lg object-cover">
                    @else
                        <div class="w-20 h-20 rounded-lg bg-[#174455] flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-3xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-[#174455]">{{ $provider->business_name }}</h2>
                    <div class="flex items-center mt-2">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($provider->rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 ml-2">
                            {{ number_format($provider->rating, 1) }} ({{ $provider->total_orders ?? 0 }} orders)
                        </span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('laundry.create-order', $provider->id) }}" 
               class="bg-[#174455] text-white px-6 py-3 rounded-lg hover:bg-[#1f556b] transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i> Place Order
            </a>
        </div>
    </div>

    {{-- Provider Info Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Provider Details --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Contact Card --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-[#174455] w-6"></i>
                        <span>{{ $provider->contact_phone }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-[#174455] w-6"></i>
                        <span>{{ $provider->contact_email }}</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-[#174455] w-6 mt-1"></i>
                        <span>{{ $provider->address }}</span>
                    </div>
                </div>
            </div>

            {{-- Business Hours Card --}}
            @if($provider->laundryConfig)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Business Hours</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Times</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($provider->laundryConfig->pickup_start_time)->format('g:i A') }} - 
                            {{ \Carbon\Carbon::parse($provider->laundryConfig->pickup_end_time)->format('g:i A') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Normal Turnaround</span>
                        <span class="font-medium">{{ $provider->laundryConfig->normal_turnaround_hours }} hours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rush Turnaround</span>
                        <span class="font-medium">{{ $provider->laundryConfig->rush_turnaround_hours }} hours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Fee</span>
                        <span class="font-medium">MMK {{ number_format($provider->laundryConfig->pickup_fee, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Description Card --}}
            @if($provider->description)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">About</h3>
                <p class="text-gray-600">{{ $provider->description }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column - Items and Pricing --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Items by Category --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Services & Pricing</h3>
                
                @php
                    $groupedItems = $items->groupBy('item_type');
                    $itemTypes = [
                        'CLOTHING' => 'Clothing',
                        'BEDDING' => 'Bedding',
                        'CURTAIN' => 'Curtain',
                        'OTHER' => 'Other'
                    ];
                @endphp

                @foreach($groupedItems as $type => $typeItems)
                <div class="mb-6 last:mb-0">
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        @if($type == 'CLOTHING')
                            <i class="fas fa-tshirt text-[#174455] mr-2"></i>
                        @elseif($type == 'BEDDING')
                            <i class="fas fa-bed text-[#174455] mr-2"></i>
                        @elseif($type == 'CURTAIN')
                            <i class="fas fa-window text-[#174455] mr-2"></i>
                        @else
                            <i class="fas fa-tag text-[#174455] mr-2"></i>
                        @endif
                        {{ $itemTypes[$type] ?? $type }}
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($typeItems as $item)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div>
                                <h5 class="font-medium text-gray-900">{{ $item->item_name }}</h5>
                                @if($item->description)
                                    <p class="text-xs text-gray-500">{{ $item->description }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-[#174455]">৳{{ number_format($item->base_price, 2) }}</p>
                                @if($item->rush_surcharge_percent > 0)
                                    <p class="text-xs text-orange-600">+{{ $item->rush_surcharge_percent }}% rush</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Recent Ratings --}}
            @if($ratings->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Recent Reviews</h3>
                
                <div class="space-y-4">
                    @foreach($ratings as $rating)
                    <div class="border-b last:border-0 pb-4 last:pb-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->overall_rating)
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-xs"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm font-medium ml-2">{{ $rating->user->name }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        @if($rating->comment)
                            <p class="text-sm text-gray-600 mt-2">{{ $rating->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection