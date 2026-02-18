<!-- Chart Container -->
<div class="chart-container">
    <!-- Chart Placeholder - This would be replaced with actual Chart.js or similar -->
    <div class="relative h-64 w-full">
        <!-- Grid Lines -->
        <div class="absolute inset-0 flex flex-col justify-between">
            @for($i = 0; $i < 6; $i++)
                <div class="border-t border-gray-100"></div>
            @endfor
        </div>
        
        <!-- Chart Bars -->
        <div class="absolute inset-0 flex items-end justify-between px-4 pb-4">
            @php
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                $earnings = [3200, 4500, 5100, 6800, 7200, 8450];
                $maxEarnings = max($earnings);
            @endphp
            
            @foreach($months as $index => $month)
                @php
                    $height = ($earnings[$index] / $maxEarnings) * 80;
                    $barColor = $index === 5 ? 'bg-blue-600' : 'bg-blue-400';
                @endphp
                <div class="flex flex-col items-center" style="width: 14%">
                    @php
                        $barHeight = $height ?? 0;
                    @endphp

                    <!-- Bar -->
                    <div class="w-10 {{ $barColor }} rounded-t-lg relative group"
                         style="<?php echo 'height: '.$barHeight.'%'; ?>">
                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            ${{ number_format($earnings[$index], 0) }}
                        </div>
                    </div>
                    
                    <!-- Month Label -->
                    <span class="mt-2 text-sm text-gray-600">{{ $month }}</span>
                    
                    <!-- Earnings Label -->
                    <span class="text-xs text-gray-500 mt-1">${{ number_format($earnings[$index]/1000, 1) }}k</span>
                </div>
            @endforeach
        </div>
        
        <!-- Y-axis Labels -->
        <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500">
            <span>${{ number_format($maxEarnings/1000, 1) }}k</span>
            <span>${{ number_format($maxEarnings * 0.8/1000, 1) }}k</span>
            <span>${{ number_format($maxEarnings * 0.6/1000, 1) }}k</span>
            <span>${{ number_format($maxEarnings * 0.4/1000, 1) }}k</span>
            <span>${{ number_format($maxEarnings * 0.2/1000, 1) }}k</span>
            <span>$0</span>
        </div>
    </div>
    
    <!-- Chart Legend -->
    <div class="flex justify-center space-x-6 mt-6 pt-6 border-t border-gray-200">
        <div class="flex items-center">
            <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
            <span class="text-sm text-gray-600">Monthly Earnings</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-4 bg-blue-300 rounded mr-2"></div>
            <span class="text-sm text-gray-600">Previous Months</span>
        </div>
    </div>
    
    <!-- Chart Info -->
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-500">Hover over bars to see exact earnings</p>
    </div>
</div>

<!-- Chart.js Integration Placeholder -->
<div class="hidden">
    <!-- This div would contain the actual Chart.js canvas in production -->
    <canvas id="monthlyEarningsChart"></canvas>
</div>

<script>
    // Placeholder for Chart.js initialization
    // In production, uncomment and implement this:
    /*
    const ctx = document.getElementById('monthlyEarningsChart').getContext('2d');
    const monthlyEarningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Monthly Earnings',
                data: [3200, 4500, 5100, 6800, 7200, 8450],
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    */
</script>