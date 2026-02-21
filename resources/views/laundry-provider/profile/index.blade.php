@extends('laundry-provider.layouts.provider')

@section('title', 'My Profile')
@section('subtitle', '')

@section('content')
<div class="space-y-6">
    {{-- Profile Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-[#174455]">My Profile</h2>
            <a href="{{ route('laundry-provider.profile.edit') }}" 
               class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors text-sm">
                Edit Profile
            </a>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            {{-- Avatar Section --}}
            <div class="md:w-1/4 flex flex-col items-center">
                <div class="w-32 h-32 bg-[#174455] rounded-full flex items-center justify-center text-white text-5xl font-bold mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Member since</p>
                    <p class="font-medium">
                        @if($user->created_at)
                            {{ $user->created_at->format('M Y') }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="md:w-3/4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Full Name</p>
                        <p class="font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email Address</p>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Phone Number</p>
                        <p class="font-medium">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Gender</p>
                        <p class="font-medium">{{ $user->gender ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $user->status ?? 'ACTIVE' }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email Verified</p>
                        @if($user->email_verified_at)
                            <span class="text-green-600">Yes</span>
                        @else
                            <span class="text-orange-600">No</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t flex gap-4">
                    <a href="{{ route('laundry-provider.profile.password') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                        <i class="fas fa-key mr-1"></i>Change Password
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('laundry-provider.profile.address') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                        <i class="fas fa-map-marker-alt mr-1"></i>Manage Addresses
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Business Information Card --}}
    @if($provider)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-[#174455]">Business Information</h3>
            <a href="{{ route('laundry-provider.profile.business') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Business Name</p>
                <p class="font-medium">{{ $provider->business_name ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Contact Email</p>
                <p class="font-medium">{{ $provider->contact_email ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Contact Phone</p>
                <p class="font-medium">{{ $provider->contact_phone ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Service Radius</p>
                <p class="font-medium">{{ $provider->service_radius_km ?? 'Not set' }} km</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 mb-1">Business Address</p>
                <p class="font-medium">{{ $provider->address ?? 'Not set' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection