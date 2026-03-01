@extends('layouts.apps')

@section('title', $room->room_type_name . ' - ' . $property->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('properties.search') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Search
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <a href="{{ route('properties.show', $property) }}" 
                   class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    {{ Str::limit($property->name, 20) }}
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ $room->room_type_name }}</span>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Room Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $room->room_type_name }} - Room {{ $room->room_number }}</h1>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-home text-gray-400 mr-2"></i>
                        <span class="text-gray-600">{{ $property->name }}, {{ $property->area }}, {{ $property->city }}</span>
                    </div>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $room->room_type_name }}
                        </span>
                        @if($room->floor_number)
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Floor: {{ $room->floor_number }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">MMK{{ number_format($room->total_price) }}/month</div>
                        <div class="text-sm text-gray-500">Base: MMK{{ number_format($room->base_price) }} + {{ $room->commission_rate }}% commission</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Room Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Room Features -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Room Features</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-users text-indigo-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Capacity</div>
                                <div class="font-medium">{{ $room->capacity }} persons</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-bed text-indigo-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Room Type</div>
                                <div class="font-medium">{{ $room->room_type_name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-venus-mars text-indigo-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Gender Policy</div>
                                <div class="font-medium">{{ $property->gender_policy_name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-check-circle text-indigo-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Status</div>
                                <div class="font-medium">{{ $room->status }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Amenities -->
                @if($property->amenities->count())
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Shared Facilities</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($property->amenities as $amenity)
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-700">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Property Rules -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">House Rules</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">Check-in: After 2:00 PM | Check-out: Before 12:00 PM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lock text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">Quiet hours: 10:00 PM to 7:00 AM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-user-friends text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">No visitors after 9:00 PM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-ban text-gray-400 mt-1 mr-2"></i>
                            <span class="text-gray-600">Strictly no smoking inside rooms</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Column - Booking Card -->
            <div class="space-y-6">
                <!-- Booking Card -->
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Request to Book</h3>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Price</span>
                            <span class="font-medium">MMK{{ number_format($room->base_price) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Commission ({{ $room->commission_rate }}%)</span>
                            <span class="font-medium">MMK{{ number_format($room->base_price * $room->commission_rate / 100) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total Price</span>
                                <span class="text-2xl font-bold text-indigo-600">MMK{{ number_format($room->total_price) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">per month</p>
                        </div>
                    </div>

                    <!-- How it works notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <div class="text-xs text-blue-700">
                                <strong>How it works:</strong> Submit a request to the owner. You'll be notified when approved, then you can make payment.
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    @auth
                        @if($room->status === 'AVAILABLE')
                            <a href="{{ route('properties.rooms.book', [$property, $room]) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-4">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit Booking Request
                            </a>
                        @else
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                    <div>
                                        <div class="font-medium text-red-800">Not Available</div>
                                        <div class="text-sm text-red-700">This room is currently {{ strtolower($room->status) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-4">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login to Book
                        </a>
                    @endauth

                    <!-- Room Status -->
                    @if($room->status === 'AVAILABLE')
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <div>
                                    <div class="font-medium text-green-800">Available Now</div>
                                    <div class="text-sm text-green-700">This room is ready for booking requests</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Contact Info -->
                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div class="flex items-center">
                            <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">Property Owner</div>
                                <div class="font-medium">{{ $property->owner->name }}</div>
                            </div>
                        </div>
                        
                        <!-- Chat with Owner -->
                        @auth
                            @if(Auth::id() !== $property->owner_id)
                                <div class="mt-2">
                                    <a href="{{ route('rental.chat.start', ['property' => $property->id]) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        <i class="fas fa-comment mr-2"></i>
                                        Chat with Owner
                                    </a>
                                </div>
                            @endif
                        @endauth

                        @if($property->owner->phone)
                            <div class="flex items-center mt-2">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Contact</div>
                                    <div class="font-medium">{{ $property->owner->phone }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Other Available Rooms -->
                @if($property->rooms->where('status', 'AVAILABLE')->where('id', '!=', $room->id)->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Other Available Rooms</h3>
                        <div class="space-y-3">
                            @foreach($property->rooms->where('status', 'AVAILABLE')->where('id', '!=', $room->id)->take(3) as $otherRoom)
                                <a href="{{ route('properties.rooms.details', [$property, $otherRoom]) }}" 
                                   class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $otherRoom->room_type_name }}</div>
                                        <div class="text-sm text-gray-600">Room {{ $otherRoom->room_number }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-indigo-600">MMK{{ number_format($otherRoom->total_price) }}</div>
                                        <div class="text-xs text-gray-500">per month</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection