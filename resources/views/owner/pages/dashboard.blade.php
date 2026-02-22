@extends('owner.layout.owner-layout')
@section('title', 'Owner Dashboard')
@section('subtitle', 'Welcome back, ' . Auth::user()->name . '!')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Owner Dashboard', 'url' => route('owner.dashboard')]
        ];
    @endphp
@endsection

@section('content')
<div class="p-6">
    <!-- Stats Cards - Using real data from controller -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Properties -->
        <div class="bg-gradient-to-r from-[#174455] to-[#286b7f] rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Properties</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalProperties ?? 0 }}</p>
                    <div class="flex space-x-2 mt-1 text-xs">
                        <span class="bg-green-400 bg-opacity-20 px-2 py-0.5 rounded">Active: {{ $activeProperties ?? 0 }}</span>
                        <span class="bg-yellow-400 bg-opacity-20 px-2 py-0.5 rounded">Pending: {{ $pendingProperties ?? 0 }}</span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-building text-xl" style="color: #ffdb9f;"></i>
                </div>
            </div>
            <a href="{{ route('owner.properties.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                Manage Properties <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Active Bookings -->
        <div class="bg-gradient-to-r from-[#1f556b] to-[#2d7a94] rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Active Bookings</p>
                    <p class="text-3xl font-bold mt-2">{{ $activeBookings ?? 0 }}</p>
                    <div class="flex space-x-2 mt-1 text-xs">
                        <span class="bg-blue-400 bg-opacity-20 px-2 py-0.5 rounded">Today: {{ $todayBookings ?? 0 }}</span>
                        <span class="bg-purple-400 bg-opacity-20 px-2 py-0.5 rounded">Month: {{ $monthBookings ?? 0 }}</span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-xl" style="color: #ffdb9f;"></i>
                </div>
            </div>
            <a href="{{ route('owner.bookings.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                View All Bookings <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Occupancy Rate -->
        <div class="bg-gradient-to-r from-[#286b7f] to-[#3a8da6] rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Occupancy Rate</p>
                    <p class="text-3xl font-bold mt-2">{{ $occupancyRate ?? 0 }}%</p>
                    <div class="flex space-x-2 mt-1 text-xs">
                        <span class="bg-green-400 bg-opacity-20 px-2 py-0.5 rounded">Booking Success: {{ $bookingSuccessRate ?? 0 }}%</span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-door-open text-xl" style="color: #ffdb9f;"></i>
                </div>
            </div>
            <!-- FIXED: Changed to properties.index since rooms are managed within properties -->
            <a href="{{ route('owner.properties.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                Manage Rooms <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

    </div>

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-[#174455] to-[#286b7f] rounded-xl shadow-lg text-white p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-2/3">
                <h2 class="text-2xl font-bold mb-2">Property Owner Dashboard</h2>
                <p class="text-[#ffdb9f] mb-4">
                    Manage your properties, track bookings, and monitor your revenue.
                    @if(($pendingProperties ?? 0) > 0)
                        <br><i class="fas fa-bell mr-1"></i> You have <strong>{{ $pendingProperties }}</strong> property(ies) pending approval.
                    @endif
                    @if(($todayBookings ?? 0) > 0)
                        <br><i class="fas fa-bell mr-1"></i> You have <strong>{{ $todayBookings }}</strong> booking(s) today.
                    @endif
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('owner.properties.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                        <i class="fas fa-plus-circle mr-2"></i> Add New Property
                    </a>
                    
                    @if(($pendingProperties ?? 0) > 0)
                        <a href="{{ route('owner.properties.index', ['status' => 'PENDING']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                            <i class="fas fa-clock mr-2"></i> View Pending Properties
                        </a>
                    @endif

                    <a href="{{ route('owner.bookings.index', ['date' => 'today']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                        <i class="fas fa-calendar-check mr-2"></i> Today's Bookings
                    </a>
                </div>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="h-40 w-40 rounded-full bg-[#ffdb9f] bg-opacity-10 flex items-center justify-center border-4 border-[#ffdb9f] border-opacity-20">
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="h-36 w-36 rounded-full object-cover">
                    @else
                        <i class="fas fa-user-tie text-6xl text-[#ffdb9f] opacity-80"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-[#174455] mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('owner.properties.create') }}" class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-[#174455] bg-opacity-10 flex items-center justify-center">
                        <i class="fas fa-plus-circle text-[#174455] text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-[#174455]">Add Property</h4>
                        <p class="text-sm text-gray-500">List new hostel/apartment</p>
                    </div>
                </div>
                <span class="inline-flex items-center text-[#174455] hover:text-[#286b7f] font-medium">
                    Create Listing <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <a href="{{ route('owner.bookings.index') }}" class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-[#1f556b] bg-opacity-10 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-[#1f556b] text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-[#174455]">Manage Bookings</h4>
                        <p class="text-sm text-gray-500">{{ $pendingBookings ?? 0 }} pending bookings</p>
                    </div>
                </div>
                <span class="inline-flex items-center text-[#1f556b] hover:text-[#286b7f] font-medium">
                    View Bookings <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <!-- FIXED: Changed to properties.index since rooms are managed within properties -->
            <a href="{{ route('owner.properties.index') }}" class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-[#286b7f] bg-opacity-10 flex items-center justify-center">
                        <i class="fas fa-door-open text-[#286b7f] text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-[#174455]">Manage Rooms</h4>
                        <p class="text-sm text-gray-500">{{ $occupancyRate ?? 0 }}% occupied</p>
                    </div>
                </div>
                <span class="inline-flex items-center text-[#286b7f] hover:text-[#174455] font-medium">
                    View Properties <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-[#ffdb9f] bg-opacity-30 flex items-center justify-center">
                        <i class="fas fa-star text-[#174455] text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-[#174455]">Performance</h4>
                        <p class="text-sm text-gray-500">{{ $averageRating ?? 0 }} avg rating ({{ $totalRatings ?? 0 }} reviews)</p>
                    </div>
                </div>
                <span class="inline-flex items-center text-[#174455] hover:text-[#286b7f] font-medium">
                    View Reports <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Recent Bookings & Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Recent Bookings -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-[#174455]">Recent Bookings</h3>
                <a href="{{ route('owner.bookings.index') }}" class="text-sm text-[#286b7f] hover:text-[#174455]">
                    View all ({{ $totalBookings ?? 0 }})
                </a>
            </div>
            <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl overflow-hidden">
                @forelse($recentBookings ?? [] as $booking)
                    <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $booking->property->name ?? 'N/A' }}</h4>
                                    <span class="ml-2 text-xs text-gray-500">#{{ $booking->booking_reference ?? 'BK-'.str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-user mr-1 text-[#286b7f]"></i> {{ $booking->user->name ?? 'N/A' }}
                                    @if($booking->user->phone ?? false)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-phone mr-1 text-[#286b7f]"></i> {{ $booking->user->phone }}
                                    @endif
                                </p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <i class="fas fa-calendar-check text-[#174455] mr-1"></i>
                                    {{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') : 'N/A' }}
                                    <span class="mx-2">→</span>
                                    {{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') : 'N/A' }}
                                    @if($booking->room ?? false)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-door-open text-[#174455] mr-1"></i>
                                        {{ $booking->room->room_number ?? 'N/A' }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'checked_in' => 'bg-blue-100 text-blue-800',
                                        'checked_out' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $status = strtolower($booking->status ?? 'pending');
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ str_replace('_', ' ', $booking->status ?? 'pending') }}
                                </span>
                                <p class="text-sm font-medium text-gray-900 mt-1">৳ {{ number_format($booking->total_amount ?? 0) }}</p>
                                <a href="{{ route('owner.bookings.show', $booking) }}" 
                                   class="text-xs text-[#286b7f] hover:text-[#174455] mt-2 inline-block">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No bookings yet</p>
                        <p class="text-sm text-gray-400 mt-2">Bookings will appear here when guests make reservations</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Recent Complaints & Property Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Complaints -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-[#174455]">Recent Complaints</h3>
                <a href="{{ route('owner.complaints.index') }}" class="text-sm text-[#286b7f] hover:text-[#174455]">
                    View all
                </a>
            </div>
            <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl overflow-hidden">
                @forelse($recentComplaints ?? [] as $complaint)
                    <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-lg bg-red-100 bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $complaint->title }}</h4>
                                    @php
                                        $priorityColor = [
                                            'URGENT' => 'bg-red-100 text-red-800',
                                            'HIGH' => 'bg-orange-100 text-orange-800',
                                            'MEDIUM' => 'bg-yellow-100 text-yellow-800',
                                            'LOW' => 'bg-green-100 text-green-800',
                                        ][$complaint->priority] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $priorityColor }}">
                                        {{ $complaint->priority }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    By {{ $complaint->user->name ?? 'N/A' }} • {{ $complaint->created_at->diffForHumans() }}
                                </p>
                                <p class="text-sm text-gray-600 mt-2">{{ Str::limit($complaint->description ?? '', 100) }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('owner.complaints.show', $complaint) }}" 
                                       class="text-xs text-[#286b7f] hover:text-[#174455]">
                                        View & Respond <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i class="fas fa-check-circle text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No complaints found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Profile Completion (if incomplete) -->
    @php
        $user = Auth::user();
        $totalFields = 3;
        $completedFields = 0;
        if($user->phone) $completedFields++;
        if($user->gender) $completedFields++;
        
        // You might want to pass this from controller
        $userAddressesCount = 0; 
        if($userAddressesCount > 0) $completedFields++;
        
        $percentage = ($completedFields / $totalFields) * 100;
    @endphp
    
    @if($percentage < 100)
        <div class="mt-8 bg-gradient-to-r from-[#174455] to-[#286b7f] bg-opacity-5 border border-[#286b7f] border-opacity-20 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[#174455] mb-2">Complete Your Profile</h3>
                    <p class="text-gray-600">Complete your profile to build trust with potential guests</p>
                    
                    <div class="mt-4 space-y-3">
                        @if(!$user->phone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-[#286b7f] mr-3"></i>
                                <span class="text-gray-700">Add phone number</span>
                            </div>
                        @endif
                        
                        @if(!$user->gender)
                            <div class="flex items-center">
                                <i class="fas fa-venus-mars text-[#286b7f] mr-3"></i>
                                <span class="text-gray-700">Specify gender</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-[#286b7f] mr-3"></i>
                            <span class="text-gray-700">Add business address</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#E5E7EB" stroke-width="3"/>
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#174455" stroke-width="3" stroke-dasharray="{{ $percentage }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-[#174455]">{{ round($percentage) }}%</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#286b7f] transition">
                        <i class="fas fa-user-edit mr-2"></i> Complete Now
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markAllNotificationsRead() {
    fetch('/owner/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush
@endsection