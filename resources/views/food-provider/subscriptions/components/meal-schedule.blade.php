<!-- Meal Schedule Component -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Meal Delivery Schedule</h2>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Week of:</span>
            <input type="date" class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="2024-03-11">
        </div>
    </div>

    <!-- Weekly Schedule -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Day
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Breakfast
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lunch
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dinner
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $weekDays = [
                        ['day' => 'Monday', 'date' => 'Mar 11'],
                        ['day' => 'Tuesday', 'date' => 'Mar 12'],
                        ['day' => 'Wednesday', 'date' => 'Mar 13'],
                        ['day' => 'Thursday', 'date' => 'Mar 14'],
                        ['day' => 'Friday', 'date' => 'Mar 15'],
                        ['day' => 'Saturday', 'date' => 'Mar 16'],
                        ['day' => 'Sunday', 'date' => 'Mar 17']
                    ];
                @endphp
                
                @foreach($weekDays as $index => $day)
                <tr class="{{ in_array($day['day'], ['Monday', 'Wednesday', 'Friday']) ? 'bg-blue-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $day['day'] }}</div>
                        <div class="text-sm text-gray-500">{{ $day['date'] }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(in_array($day['day'], ['Monday', 'Wednesday', 'Friday']))
                        <div class="text-sm text-gray-900">Omelette</div>
                        <div class="text-xs text-gray-500">08:00 AM</div>
                        @else
                        <span class="text-sm text-gray-400">No delivery</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(in_array($day['day'], ['Monday', 'Wednesday', 'Friday']))
                        <div class="text-sm text-gray-900">Chicken Curry</div>
                        <div class="text-xs text-gray-500">01:00 PM</div>
                        @else
                        <span class="text-sm text-gray-400">No delivery</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(in_array($day['day'], ['Monday', 'Wednesday', 'Friday']))
                        <div class="text-sm text-gray-900">Grilled Fish</div>
                        <div class="text-xs text-gray-500">07:00 PM</div>
                        @else
                        <span class="text-sm text-gray-400">No delivery</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($index < 2)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Delivered
                        </span>
                        @elseif($index == 2)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Today
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Scheduled
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-blue-100 mr-2"></div>
                <span class="text-sm text-gray-600">Scheduled Delivery Day</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-100 mr-2"></div>
                <span class="text-sm text-gray-600">Delivered</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                <span class="text-sm text-gray-600">Today</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            <button class="text-blue-600 hover:text-blue-900 mr-4">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Schedule
            </button>
            <button class="text-blue-600 hover:text-blue-900">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Set Reminders
            </button>
        </div>
        <div>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Update Schedule
            </button>
        </div>
    </div>
</div>