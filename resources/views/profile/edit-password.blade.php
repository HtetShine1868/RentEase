@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Change Password</h1>
                <p class="mt-2 text-gray-600">Update your account password</p>
            </div>
            <div>
                <a href="{{ route('profile.show') }}" 
                   class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Password Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input :type="showCurrentPassword ? 'text' : 'password'" 
                               id="current_password" name="current_password" required
                               class="pl-10 pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input :type="showNewPassword ? 'text' : 'password'" 
                               id="password" name="password" required
                               class="pl-10 pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="showNewPassword = !showNewPassword"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Password Requirements -->
                    <div class="mt-2 text-xs text-gray-500">
                        <p>Password must contain:</p>
                        <ul class="list-disc pl-4 mt-1 space-y-1">
                            <li>At least 8 characters</li>
                            <li>At least one uppercase letter</li>
                            <li>At least one lowercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                               id="password_confirmation" name="password_confirmation" required
                               class="pl-10 pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Security Notice</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>After changing your password, you'll need to log in again on all devices.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i>
                        Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js for password visibility -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('passwordForm', () => ({
        showCurrentPassword: false,
        showNewPassword: false,
        showConfirmPassword: false
    }));
});
</script>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection