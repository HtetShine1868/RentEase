{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Profile Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
                        <p class="text-sm text-gray-600 mb-6">Update your account's profile information and email address.</p>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                        :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                        :value="old('email', $user->email)" required autocomplete="email" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Phone')" />
                                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                                        :value="old('phone', $user->phone)" required autocomplete="tel" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <!-- Location (Optional) -->
                                <div>
                                    <x-input-label for="location" :value="__('Location (Optional)')" />
                                    <div class="flex space-x-2 mt-1">
                                        <x-text-input id="location_lat" name="location_lat" type="text" 
                                            class="block w-full" placeholder="Latitude" 
                                            :value="old('location_lat', $user->location['lat'] ?? '')" />
                                        <x-text-input id="location_lng" name="location_lng" type="text" 
                                            class="block w-full" placeholder="Longitude" 
                                            :value="old('location_lng', $user->location['lng'] ?? '')" />
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">For location-based service recommendations</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('location_lat')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('location_lng')" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Update Password -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Password</h3>
                        <p class="text-sm text-gray-600 mb-6">Ensure your account is using a long, random password to stay secure.</p>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="current_password" :value="__('Current Password')" />
                                    <x-text-input id="current_password" name="current_password" type="password" 
                                        class="mt-1 block w-full" autocomplete="current-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('New Password')" />
                                    <x-text-input id="password" name="password" type="password" 
                                        class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                        class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Update Password') }}</x-primary-button>

                                @if (session('status') === 'password-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Account Status -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Account Role</p>
                                    <p class="text-sm text-gray-600">Your current role in the system</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @switch($user->role)
                                        @case('superadmin') bg-red-100 text-red-800 @break
                                        @case('owner') bg-blue-100 text-blue-800 @break
                                        @case('food_provider') bg-green-100 text-green-800 @break
                                        @case('laundry_provider') bg-yellow-100 text-yellow-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Member Since</p>
                                    <p class="text-sm text-gray-600">When you joined RMS</p>
                                </div>
                                <span class="text-gray-600">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Account Status</p>
                                    <p class="text-sm text-gray-600">Current status of your account</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>