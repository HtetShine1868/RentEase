@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-8 sm:px-10 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-lg">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Storage::url(Auth::user()->avatar_url) }}" alt="{{ Auth::user()->name }}" 
                                 class="h-20 w-20 rounded-full object-cover">
                        @else
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        @endif
                    </div>
                </div>
                <div class="ml-6">
                    <h1 class="text-2xl font-bold text-white">{{ Auth::user()->name }}</h1>
                    <p class="text-indigo-100">{{ Auth::user()->email }}</p>
                    <div class="mt-2">
                        @foreach(Auth::user()->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-indigo-600 mr-2">
                                {{ ucfirst(strtolower($role->name)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Profile Stats -->
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-around text-center">
                <div>
                    <p class="text-sm font-medium text-gray-500">Member Since</p>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ Auth::user()->status }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Email Verified</p>
                    <p class="mt-1">
                        @if(Auth::user()->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exclamation mr-1"></i> Pending
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Full Name</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email Address</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone Number</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Gender</label>
                    <p class="mt-1 text-gray-900">
                        @if(Auth::user()->gender)
                            {{ ucfirst(strtolower(Auth::user()->gender)) }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Address Information</h3>
                <a href="{{ route('profile.address.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-edit mr-1"></i> Manage
                </a>
            </div>
            
            @if(Auth::user()->defaultAddress)
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Default Address</label>
                        <p class="mt-1 text-gray-900">{{ Auth::user()->defaultAddress->address_line1 }}</p>
                        @if(Auth::user()->defaultAddress->address_line2)
                            <p class="text-gray-900">{{ Auth::user()->defaultAddress->address_line2 }}</p>
                        @endif
                        <p class="text-gray-900">
                            {{ Auth::user()->defaultAddress->city }}, 
                            {{ Auth::user()->defaultAddress->state }}
                            {{ Auth::user()->defaultAddress->postal_code }}
                        </p>
                        <p class="text-gray-900">{{ Auth::user()->defaultAddress->country }}</p>
                    </div>
                    
                    @if(Auth::user()->addresses->count() > 1)
                        <div class="pt-3 border-t">
                            <label class="text-sm font-medium text-gray-500">Other Addresses</label>
                            <ul class="mt-2 space-y-2">
                                @foreach(Auth::user()->addresses->where('is_default', false) as $address)
                                    <li class="text-sm text-gray-600">
                                        {{ $address->address_line1 }}, {{ $address->city }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-map-marker-alt text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">No address added yet</p>
                    <a href="{{ route('profile.address.edit') }}" 
                       class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                        <i class="fas fa-plus mr-1"></i> Add Address
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Account Security -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Security</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Password</h4>
                    <p class="mt-1 text-sm text-gray-500">Last changed: 
                        @if(Auth::user()->updated_at)
                            {{ Auth::user()->updated_at->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </p>
                </div>
                <a href="{{ route('profile.password.edit') }}" 
                   class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-key mr-1"></i> Change
                </a>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                    <p class="mt-1 text-sm text-gray-500">Add extra security to your account</p>
                </div>
                <button type="button" 
                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-shield-alt mr-1"></i> Enable
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('profile.edit') }}" 
           class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-user-edit text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Profile</h3>
                    <p class="mt-1 text-sm text-gray-500">Update personal information</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('profile.address.edit') }}" 
           class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Manage Addresses</h3>
                    <p class="mt-1 text-sm text-gray-500">Add or update addresses</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('profile.password.edit') }}" 
           class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-key text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
                    <p class="mt-1 text-sm text-gray-500">Update your password</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection