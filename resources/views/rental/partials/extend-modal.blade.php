<!-- Extend Stay Modal -->
<div id="extendModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
            <h3 class="text-lg font-medium text-gray-900">Extend Your Stay</h3>
            <p id="extendPropertyName" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <form id="extendForm" method="POST" action="{{ route('bookings.extend') }}">
            @csrf
            <input type="hidden" name="booking_id" id="extendBookingId">
            <input type="hidden" name="daily_price" id="extendDailyPrice">
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Check-out</label>
                        <input type="text" id="currentCheckout" readonly
                               class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Check-out Date</label>
                        <input type="date" name="new_check_out" required
                               id="newCheckoutDate"
                               class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Extension Days</label>
                        <input type="number" id="extensionDays" readonly
                               class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-blue-700">Daily Rate:</span>
                            <span class="font-medium text-blue-700" id="dailyRateDisplay"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-700 font-semibold">Total Extension Cost:</span>
                            <span class="font-bold text-blue-700 text-lg" id="totalExtensionCost"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeExtendModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700">
                    Extend Stay
                </button>
            </div>
        </form>
    </div>
</div>