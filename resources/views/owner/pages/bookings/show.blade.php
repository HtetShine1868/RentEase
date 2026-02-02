@extends('owner.layouts.master')

@section('title', 'Booking Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
            <p class="text-gray-600">Booking ID: #{{ $booking->id }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('owner.bookings.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                ← Back to Bookings
            </a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Print Invoice
            </button>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        @php
            $status = $booking->status ?? 'pending';
            $statusColors = [
                'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
            ];
            $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        @endphp
        
        <div class="inline-flex items-center px-4 py-2 rounded-lg border {{ $statusColor }}">
            <span class="font-semibold">Status:</span>
            <span class="ml-2">{{ ucfirst($status) }}</span>
        </div>
        <span class="ml-4 text-gray-500">
            Booked on: {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Guest Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Guest Information</h2>
                
                <div class="flex items-start gap-4">
                    <!-- Avatar -->
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        @php
                            $user = \App\Models\User::find($booking->user_id);
                            $initial = $user ? strtoupper(substr($user->name, 0, 1)) : 'G';
                        @endphp
                        <span class="text-2xl font-bold text-purple-600">{{ $initial }}</span>
                    </div>
                    
                    <!-- Guest Details -->
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ $user->name ?? 'Guest #' . $booking->user_id }}
                        </h3>
                        <div class="mt-2 space-y-2 text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                {{ $user->email ?? 'Email not available' }}
                            </div>
                            @if($user && $user->phone)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                {{ $user->phone }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Booking Dates -->
                <div class="mt-6 pt-4 border-t grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Check-in</p>
                        <p class="font-semibold">
                            {{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') : 'Not set' }}
                        </p>
                        <p class="text-sm text-gray-500">After 2:00 PM</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Check-out</p>
                        <p class="font-semibold">
                            {{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') : 'Not set' }}
                        </p>
                        <p class="text-sm text-gray-500">Before 11:00 AM</p>
                    </div>
                    <div class="col-span-2 mt-2">
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-semibold">
                            @if($booking->check_in && $booking->check_out)
                                {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} nights
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Property Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Property Information</h2>
                
                @php
                    $property = \App\Models\Property::find($booking->property_id);
                    $room = \App\Models\Room::find($booking->room_id);
                @endphp
                
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Property Image -->
                    <div class="md:w-1/3">
                        @if($property && $property->primaryImage)
                            <img src="{{ asset('storage/' . $property->primaryImage->image_path) }}" 
                                 alt="{{ $property->name }}"
                                 class="w-full h-48 object-cover rounded-lg">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Property Details -->
                    <div class="md:w-2/3">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $property->name ?? 'Property #' . $booking->property_id }}
                        </h3>
                        <p class="text-gray-600 mt-1">
                            {{ $property->address ?? 'Address not available' }}
                        </p>
                        
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Property Type</p>
                                <p class="font-semibold">
                                    {{ $property ? ($property->type == 'HOSTEL' ? 'Hostel' : 'Apartment') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Room/Unit</p>
                                <p class="font-semibold">
                                    {{ $room->name ?? ($booking->room_id ? 'Room #' . $booking->room_id : 'Not specified') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Guests</p>
                                <p class="font-semibold">{{ $booking->guests ?? 1 }} guest(s)</p>
                            </div>
                            
                            @if($property)
                            <div>
                                <p class="text-sm text-gray-500">Bedrooms</p>
                                <p class="font-semibold">{{ $property->bedrooms ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Bathrooms</p>
                                <p class="font-semibold">{{ $property->bathrooms ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Size</p>
                                <p class="font-semibold">{{ $property->unit_size ?? 'N/A' }} sq ft</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Description -->
                        @if($property && $property->description)
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-1">Description</p>
                                <p class="text-gray-700 text-sm line-clamp-2">{{ $property->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Payment Summary</h2>
                
                <!-- Cost Breakdown -->
                <div class="space-y-3">
                    @php
                        $nightlyRate = $booking->nightly_rate ?? $booking->base_price ?? 0;
                        $cleaningFee = $booking->cleaning_fee ?? 0;
                        $serviceFee = $booking->service_fee ?? 0;
                        $discount = $booking->discount ?? 0;
                        $totalAmount = $booking->total_amount ?? 0;
                        $nights = 1;
                        
                        if ($booking->check_in && $booking->check_out) {
                            try {
                                $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out);
                            } catch (\Exception $e) {}
                        }
                    @endphp
                    
                    @if($nightlyRate > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nightly Rate</span>
                        <span class="font-medium">${{ number_format($nightlyRate, 2) }} × {{ $nights }} nights</span>
                    </div>
                    @endif
                    
                    @if($cleaningFee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cleaning Fee</span>
                        <span class="font-medium">${{ number_format($cleaningFee, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($serviceFee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Service Fee</span>
                        <span class="font-medium">${{ number_format($serviceFee, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span class="font-medium">-${{ number_format($discount, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total Amount</span>
                            <span>${{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Status -->
                @php
                    $paymentStatus = $booking->payment_status ?? 'pending';
                    $statusColors = [
                        'paid' => 'green',
                        'pending' => 'yellow', 
                        'refunded' => 'blue',
                        'failed' => 'red'
                    ];
                    $color = $statusColors[$paymentStatus] ?? 'gray';
                @endphp
                
                <div class="mt-6 p-4 rounded-lg bg-{{ $color }}-50 border border-{{ $color }}-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-{{ $color }}-800">
                                {{ ucfirst($paymentStatus) }}
                            </p>
                            <p class="text-sm text-{{ $color }}-600 mt-1">
                                @if($paymentStatus == 'paid' && $booking->paid_at)
                                    Paid on {{ \Carbon\Carbon::parse($booking->paid_at)->format('M d, Y') }}
                                @else
                                    {{ $paymentStatus == 'paid' ? 'Payment completed' : 'Pending payment' }}
                                @endif
                            </p>
                        </div>
                        @if($paymentStatus != 'paid')
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                Mark as Paid
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-6 space-y-3">
                    @php
                        $bookingStatus = $booking->status ?? 'pending';
                    @endphp
                    
                    @if($bookingStatus == 'pending')
                        <button class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            Confirm Booking
                        </button>
                    @endif
                    
                    @if($bookingStatus == 'confirmed')
                        <button class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Send Check-in Instructions
                        </button>
                    @endif
                    
                    @if(in_array($bookingStatus, ['pending', 'confirmed']))
                        <button class="w-full py-3 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 font-medium">
                            Cancel Booking
                        </button>
                    @endif
                    
                    <button class="w-full py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                        Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush 