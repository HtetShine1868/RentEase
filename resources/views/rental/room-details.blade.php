@extends('dashboard')

@section('title', $room->room_type_name . ' - ' . $property->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('properties.search') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-search mr-2"></i>
                    Search Rental
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <a href="{{ route('properties.show', $property) }}" 
                       class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        {{ $property->name }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500">{{ $room->room_type_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Room Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $room->room_type_name }} Room</h1>
                <div class="flex items-center mt-2">
                    <i class="fas fa-home text-gray-400 mr-2"></i>
                    <span class="text-gray-600">{{ $property->name }}, {{ $property->area }}, {{ $property->city }}</span>
                </div>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $room->room_type_name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Room No: {{ $room->room_number }}
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
                    <div class="text-2xl font-bold text-gray-900">৳{{ number_format($room->total_price) }}/month</div>
                    <div class="text-sm text-gray-500">Base: ৳{{ number_format($room->base_price) }} + {{ $room->commission_rate }}% commission</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Room Features -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Room Features</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-users text-indigo-600 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Capacity</div>
                            <div class="font-medium">{{ $room->capacity }} persons</div>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-bed text-indigo-600 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Room Type</div>
                            <div class="font-medium">{{ $room->room_type_name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-venus-mars text-indigo-600 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Gender Policy</div>
                            <div class="font-medium">{{ $property->gender_policy_name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-check-circle text-indigo-600 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div class="font-medium">{{ $room->status }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shared Facilities -->
            @if($property->amenities->count())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Shared Facilities</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($property->amenities as $amenity)
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Property Rules -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Property Rules</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Check-in: After 2:00 PM | Check-out: Before 12:00 PM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-door-closed text-gray-400 mt-1 mr-2"></i>
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
            <!-- Price & Booking Card -->
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rent This Room</h3>
                
                <!-- Price Breakdown -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Price</span>
                        <span class="font-medium">৳{{ number_format($room->base_price) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Commission ({{ $room->commission_rate }}%)</span>
                        <span class="font-medium">৳{{ number_format($room->base_price * $room->commission_rate / 100) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-medium text-gray-900">Total Price</span>
                            <span class="text-2xl font-bold text-indigo-600">৳{{ number_format($room->total_price) }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">per month</p>
                    </div>
                </div>

                <!-- CTA Button -->
                <a href="{{ route('properties.rooms.book', [$property, $room]) }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-4">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Rent This Room
                </a>

                <!-- Room Status -->
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <div>
                            <div class="font-medium text-green-800">Available Now</div>
                            <div class="text-sm text-green-700">This room is ready for immediate booking</div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Property Owner</div>
                            <div class="font-medium">{{ $property->owner->name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Contact</div>
                            <div class="font-medium">{{ $property->owner->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Room Types -->
            @if($property->rooms->count() > 1)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Other Room Types</h3>
                    <div class="space-y-3">
                        @foreach($property->rooms->where('id', '!=', $room->id)->take(3) as $otherRoom)
                        <a href="{{ url('/properties/' . $property->id . '/rooms/' . $otherRoom->id) }}" 
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                                                        <div>
                                    <div class="font-medium text-gray-900">{{ $otherRoom->room_type_name }}</div>
                                    <div class="text-sm text-gray-600">Room {{ $otherRoom->room_number }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-900">৳{{ number_format($otherRoom->total_price) }}</div>
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
@endsection