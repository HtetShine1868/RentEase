@extends('layouts.food-provider')

@section('title', 'My Profile')
@section('subtitle', '')

@section('content')
<div class="space-y-6">
    {{-- Profile Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-[#174455]">My Profile</h2>
                <p class="text-gray-600">View and manage your profile information</p>
            </div>
            <a href="{{ route('food-provider.profile.edit') }}" 
               class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors text-sm">
                <i class="fas fa-edit mr-2"></i> Edit Profile
            </a>
        </div>
    </div>

    @php
        $serviceProvider = auth()->user()->serviceProvider;
        $foodConfig = $serviceProvider->foodServiceConfig ?? null;
    @endphp

    {{-- Profile Content Card --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-8">
                {{-- Logo Section --}}
                <div class="md:w-1/3 flex flex-col items-center">
                    <div class="w-40 h-40 rounded-lg overflow-hidden border-2 border-gray-200 mb-4">
                        @if($serviceProvider->avatar_url)
                            <img src="{{ Storage::url($serviceProvider->avatar_url) }}" 
                                 alt="{{ $serviceProvider->business_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-utensils text-[#174455] text-5xl"></i>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">Restaurant Logo</p>
                </div>

                {{-- Details Section --}}
                <div class="md:w-2/3 space-y-6">
                    {{-- Basic Info --}}
                    <div>
                        <h4 class="text-md font-semibold text-[#174455] mb-3">Basic Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Restaurant Name</p>
                                <p class="font-medium">{{ $serviceProvider->business_name ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">City</p>
                                <p class="font-medium">{{ $serviceProvider->city ?? 'Not set' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500">Description</p>
                                <p class="font-medium">{{ $serviceProvider->description ?? 'No description' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div>
                        <h4 class="text-md font-semibold text-[#174455] mb-3">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <p class="font-medium">{{ $serviceProvider->contact_phone ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="font-medium">{{ $serviceProvider->contact_email ?? 'Not set' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Location Info --}}
                    <div>
                        <h4 class="text-md font-semibold text-[#174455] mb-3">Location & Service Area</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Full Address</p>
                                <p class="font-medium">{{ $serviceProvider->address ?? 'Not set' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Latitude</p>
                                    <p class="font-medium">{{ $serviceProvider->latitude ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Longitude</p>
                                    <p class="font-medium">{{ $serviceProvider->longitude ?? 'Not set' }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Service Radius</p>
                                <p class="font-medium">{{ $serviceProvider->service_radius_km ?? 'Not set' }} km</p>
                            </div>
                        </div>
                    </div>

                    {{-- Service Configuration --}}
                    @if($foodConfig)
                    <div>
                        <h4 class="text-md font-semibold text-[#174455] mb-3">Service Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Operating Hours</p>
                                <p class="font-medium">{{ $foodConfig->opening_time ?? '08:00' }} - {{ $foodConfig->closing_time ?? '22:00' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Preparation Time</p>
                                <p class="font-medium">{{ $foodConfig->avg_preparation_minutes ?? 30 }} minutes</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Delivery Buffer</p>
                                <p class="font-medium">{{ $foodConfig->delivery_buffer_minutes ?? 15 }} min/km</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Subscription Discount</p>
                                <p class="font-medium">{{ $foodConfig->subscription_discount_percent ?? 10 }}%</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500">Cuisine Type</p>
                                <p class="font-medium">{{ $foodConfig->cuisine_type ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Quick Actions --}}
                    <div class="pt-4 border-t flex gap-4">
                        <a href="{{ route('food-provider.profile.edit') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('food-provider.profile.address') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                            <i class="fas fa-map-marker-alt mr-1"></i> Manage Addresses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection