{{-- resources/views/laundry-provider/components/progress-bar.blade.php --}}
@php
    $statusSteps = [
        'PICKED_UP' => 25,
        'IN_PROGRESS' => 50,
        'READY' => 75,
        'OUT_FOR_DELIVERY' => 90
    ];
    
    $progress = $progress ?? $statusSteps[$status] ?? 0;
    
    $statusMessages = [
        'PICKED_UP' => 'Processing started',
        'IN_PROGRESS' => 'Currently washing/drying',
        'READY' => 'Ready for delivery',
        'OUT_FOR_DELIVERY' => 'On the way'
    ];
    
    $message = $statusMessages[$status] ?? 'Processing';
@endphp

<div class="w-full">
    <div class="flex justify-between text-xs text-gray-600 mb-1">
        <span>{{ $message }}</span>
        <span class="font-medium">{{ $progress }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-{{ $status == 'OUT_FOR_DELIVERY' ? 'orange' : 'blue' }}-500 h-2 rounded-full transition-all duration-500"
             style="width: {{ $progress }}%"></div>
    </div>
</div>