@extends('dashboard')

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
            <a href="{{ route('profile.edit') }}" 
               class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors text-sm">
                <i class="fas fa-edit mr-2"></i> Edit Profile
            </a>
        </div>
    </div>

    {{-- Profile Content Card --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-8">
                {{-- Avatar Section --}}
                <div class="md:w-1/3 flex flex-col items-center">
                    <div class="relative mb-4">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ Storage::url(auth()->user()->avatar_url) }}" 
                                 alt="{{ auth()->user()->name }}"
                                 class="w-32 h-32 rounded-full border-4 border-[#174455] object-cover">
                        @else
                            <div class="w-32 h-32 rounded-full bg-[#174455] flex items-center justify-center text-white text-4xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Member since</p>
                        <p class="font-medium">
                            @if(auth()->user()->created_at)
                                {{ auth()->user()->created_at->format('F Y') }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Details Section --}}
                <div class="md:w-2/3 space-y-6">
                    {{-- Basic Info --}}
                    <div>
                        <h4 class="text-md font-semibold text-[#174455] mb-3">Personal Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Full Name</p>
                                <p class="font-medium">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email Address</p>
                                <p class="font-medium">{{ auth()->user()->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <p class="font-medium">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Gender</p>
                                <p class="font-medium">
                                    @if(auth()->user()->gender)
                                        {{ ucfirst(strtolower(auth()->user()->gender)) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Status</p>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ auth()->user()->status ?? 'ACTIVE' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email Verified</p>
                                @if(auth()->user()->email_verified_at)
                                    <span class="text-green-600">Yes</span>
                                @else
                                    <span class="text-orange-600">No</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="pt-4 border-t flex gap-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('profile.address') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                            <i class="fas fa-map-marker-alt mr-1"></i> Manage Addresses
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('profile.password') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                            <i class="fas fa-key mr-1"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection