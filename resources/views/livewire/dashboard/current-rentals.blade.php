<!-- resources/views/livewire/dashboard/current-rentals.blade.php -->
<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Current Rentals</h3>
            <a href="{{ route('bookings') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                View all
            </a>
        </div>
    </div>
    
    <div class="px-6 py-4">
        @if($bookings->isEmpty())
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No active rentals</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by booking your first property.</p>
                <div class="mt-6">
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Find Properties
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($bookings as $booking)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                @if($booking->property->type === 'HOSTEL')
                                    <span class="text-indigo-600 font-bold">H</span>
                                @else
                                    <span class="text-indigo-600 font-bold">A</span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $booking->status === 'CONFIRMED' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst(strtolower($booking->status)) }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">
                                        Check-in: {{ $booking->check_in->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">৳{{ number_format($booking->total_amount) }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->duration_days }} days</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>