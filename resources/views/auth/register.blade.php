<x-guest-layout>
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
            <p class="text-gray-600 mt-2">Register to access RMS</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" value="Full Name" />
                <x-text-input id="name"
                              class="block mt-1 w-full"
                              type="text"
                              name="name"
                              :value="old('name')"
                              required
                              autofocus
                              autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" value="Email Address" />
                <x-text-input id="email"
                              class="block mt-1 w-full"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required
                              autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password"
                              class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required
                              autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation"
                              class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation"
                              required
                              autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 transition duration-300 font-medium">
                Create Account
            </button>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}"
                       class="text-indigo-600 hover:text-indigo-500 font-medium">
                        Sign in
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
