@props([
    'title' => 'No data available',
    'description' => 'Get started by creating your first item.',
    'icon' => 'fas fa-inbox',
    'iconColor' => 'text-gray-400',
    'buttonText' => 'Create New',
    'buttonLink' => '#',
    'buttonIcon' => 'fas fa-plus',
    'secondaryButtonText' => '',
    'secondaryButtonLink' => '#',
    'compact' => false
])

<div class="text-center py-12 {{ $compact ? 'py-8' : 'py-16' }}">
    <div class="{{ $iconColor }} text-5xl {{ $compact ? 'text-4xl' : 'text-5xl' }} mb-4">
        <i class="{{ $icon }}"></i>
    </div>
    
    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $title }}</h3>
    
    @if($description)
        <p class="mt-1 text-sm text-gray-500 max-w-md mx-auto">
            {{ $description }}
        </p>
    @endif
    
    <div class="mt-6">
        @if($buttonText)
            <a href="{{ $buttonLink }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                @if($buttonIcon)
                    <i class="{{ $buttonIcon }} mr-2"></i>
                @endif
                {{ $buttonText }}
            </a>
        @endif
        
        @if($secondaryButtonText)
            <a href="{{ $secondaryButtonLink }}" 
               class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $secondaryButtonText }}
            </a>
        @endif
    </div>
    
    @if(!$compact)
        <div class="mt-8">
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Need help getting started?</h4>
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <i class="fas fa-book mr-1"></i> View documentation
                    </a>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <i class="fas fa-video mr-1"></i> Watch tutorial
                    </a>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <i class="fas fa-question-circle mr-1"></i> Contact support
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="text-center py-12">
    @if($icon === 'bell')
    <div class="mx-auto h-12 w-12 text-gray-400">
        <svg class="h-full w-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
    </div>
    @elseif($icon === 'review')
    <div class="mx-auto h-12 w-12 text-gray-400">
        <svg class="h-full w-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
    </div>
    @else
    <div class="mx-auto h-12 w-12 text-gray-400">
        <svg class="h-full w-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    </div>
    @endif
    
    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $title ?? 'No data available' }}</h3>
    <p class="mt-2 text-sm text-gray-500">{{ $message ?? 'There is no data to display at the moment.' }}</p>
    
    @if(isset($action) && $action)
    <div class="mt-6">
        <button onclick="{{ $action['onclick'] ?? '' }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ $action['text'] ?? 'Add New' }}
        </button>
    </div>
    @endif
</div>

