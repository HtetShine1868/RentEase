@props([
    'size' => 'medium', // small, medium, large
    'color' => 'indigo',
    'text' => '',
    'fullscreen' => false
])

@php
    $sizes = [
        'small' => 'h-4 w-4',
        'medium' => 'h-8 w-8',
        'large' => 'h-12 w-12'
    ];
    
    $colors = [
        'indigo' => 'text-indigo-600',
        'white' => 'text-white',
        'gray' => 'text-gray-600'
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['medium'];
    $colorClass = $colors[$color] ?? $colors['indigo'];
@endphp

@if($fullscreen)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="{{ $sizeClass }} {{ $colorClass }} animate-spin mx-auto">
                <i class="fas fa-circle-notch"></i>
            </div>
            @if($text)
                <p class="mt-2 text-white font-medium">{{ $text }}</p>
            @endif
        </div>
    </div>
@else
    <div class="flex items-center justify-center {{ $fullscreen ? 'h-screen' : '' }}">
        <div class="text-center">
            <div class="{{ $sizeClass }} {{ $colorClass }} animate-spin mx-auto">
                <i class="fas fa-circle-notch"></i>
            </div>
            @if($text)
                <p class="mt-2 text-gray-600 text-sm">{{ $text }}</p>
            @endif
        </div>
    </div>
@endif