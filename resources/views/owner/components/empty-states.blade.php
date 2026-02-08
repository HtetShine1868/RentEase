{{-- Empty State Component --}}
@if(session('empty_state'))
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">{{ session('empty_state') }}</p>
        </div>
    </div>
</div>
@endif

{{-- No Data State --}}
@if(isset($showEmpty) && $showEmpty)
<div class="text-center py-12">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
    </div>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Data Available</h3>
    <p class="text-gray-500 mb-6">There are no items to display at the moment.</p>
    <a href="{{ $emptyAction ?? '#' }}" 
       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
        <i class="fas fa-plus mr-2"></i> Create New
    </a>
</div>
@endif