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