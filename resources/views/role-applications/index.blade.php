@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Apply for Service Provider Role</h1>
        <p class="mt-2 text-gray-600">Choose a role to apply for and provide the required information.</p>
    </div>

    <!-- Available Roles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Property Owner Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200 hover:border-indigo-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-home text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Property Owner</h3>
                        <p class="text-sm text-gray-500">List and manage properties</p>
                    </div>
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        List hostels and apartments
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Manage bookings and income
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        3% commission rate
                    </li>
                </ul>
                @if(in_array('OWNER', $availableRoles))
                    <a href="{{ route('role.apply.create', 'OWNER') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Apply Now
                    </a>
                @else
                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 text-center">
                        @if($user->isOwner())
                            Already an Owner
                        @else
                            Cannot Apply
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Food Provider Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200 hover:border-green-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-utensils text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Food Provider</h3>
                        <p class="text-sm text-gray-500">Offer food services</p>
                    </div>
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Subscription & pay-per-eat
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Set coverage radius
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        8% commission rate
                    </li>
                </ul>
                @if(in_array('FOOD', $availableRoles))
                    <a href="{{ route('role.apply.create', 'FOOD') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Apply Now
                    </a>
                @else
                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 text-center">
                        @if($user->isFoodProvider())
                            Already a Food Provider
                        @else
                            Cannot Apply
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Laundry Provider Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200 hover:border-purple-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-tshirt text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Laundry Provider</h3>
                        <p class="text-sm text-gray-500">Offer laundry services</p>
                    </div>
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Normal & rush service
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Pickup and delivery
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        10% commission rate
                    </li>
                </ul>
                @if(in_array('LAUNDRY', $availableRoles))
                    <a href="{{ route('role.apply.create', 'LAUNDRY') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Apply Now
                    </a>
                @else
                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 text-center">
                        @if($user->isLaundryProvider())
                            Already a Laundry Provider
                        @else
                            Cannot Apply
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Applications -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">My Applications</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($applications as $application)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center">
                                <h3 class="text-md font-medium text-gray-900">
                                    {{ $application->roleTypeName }}
                                </h3>
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $application->statusBadge }}">
                                    {{ $application->status }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                <i class="fas fa-building mr-1"></i>
                                {{ $application->business_name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                Applied on {{ $application->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('role.apply.show', $application->id) }}" 
                               class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-eye mr-1"></i>
                                View
                            </a>
                            @if($application->status === 'PENDING')
                                <form action="{{ route('role.apply.destroy', $application->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to cancel this application?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-times mr-1"></i>
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if($application->rejection_reason)
                        <div class="mt-3 p-3 bg-red-50 rounded border border-red-100">
                            <p class="text-sm text-red-700">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <strong>Rejection Reason:</strong> {{ $application->rejection_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">No applications yet.</p>
                    <p class="text-sm text-gray-400 mt-1">Select a role above to get started.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection