<!-- resources/views/livewire/dashboard/welcome-card.blade.php -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-indigo-100">
                    @if(auth()->user()->created_at->diffInDays(now()) < 7)
                        Welcome to RMS! We're glad to have you here.
                    @else
                        Good to see you again. Here's what's happening today.
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold">{{ now()->format('d') }}</div>
                    <div class="text-sm uppercase">{{ now()->format('M') }}</div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-indigo-100">Active Bookings</p>
                        <p class="text-xl font-bold">{{ $activeBookingsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-indigo-100">Total Spent</p>
                        <p class="text-xl font-bold">৳{{ number_format($totalSpent ?? 0) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-indigo-100">Your Rating</p>
                        <p class="text-xl font-bold">{{ number_format($averageRating ?? 0, 1) }}/5.0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>