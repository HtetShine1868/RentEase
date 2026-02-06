<div class="subscription-card bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">#SUB-{{ $subscriptionNumber ?? '001234' }}</h3>
                <p class="text-sm text-gray-500">{{ $customerName ?? 'John Doe' }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                @if(($status ?? 'active') === 'active') bg-green-100 text-green-800
                @elseif(($status ?? 'active') === 'paused') bg-yellow-100 text-yellow-800
                @elseif(($status ?? 'active') === 'cancelled') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($status ?? 'active') }}
            </span>
        </div>

        <!-- Meal Plan Info -->
        <div class="mb-4">
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>{{ $mealPlan ?? 'Lunch + Dinner' }}</span>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $schedule ?? 'Mon, Wed, Fri at 1:00 PM & 7:00 PM' }}</span>
            </div>
        </div>

        @php
            $progressValue = $progress ?? 15;
            $totalValue = $totalDays ?? 30;
            $percentage = ($progressValue / $totalValue) * 100;
        @endphp

        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Progress</span>
                <span>{{ $progressValue }} of {{ $totalValue }} days</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full"
                     style="<?php echo 'width: '.$percentage.'%'; ?>">
                </div>
            </div>
        </div>

        <!-- Amount and Actions -->
        <div class="flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold text-gray-900">${{ $amount ?? '90.00' }}</p>
                <p class="text-sm text-gray-500">per month</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ $viewUrl ?? '#' }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium">
                    View
                </a>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium">
                    ...
                </button>
            </div>
        </div>
    </div>
</div>