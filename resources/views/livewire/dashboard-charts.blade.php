<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Device Status Distribution --}}
    <div class="premium-card p-8 rounded-2xl flex flex-col h-full">
        <div class="flex items-center justify-between mb-8">
            <div class="flex flex-col">
                <span class="stat-label">Health Metrics</span>
                <flux:heading size="lg" class="tracking-tight">{{ __('Operational Status') }}</flux:heading>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 rounded-full">
                <span class="size-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Live</span>
            </div>
        </div>
        
        <div class="flex-1 flex flex-col md:flex-row items-center gap-8">
            <div class="w-full md:w-1/2 h-64">
                <canvas id="statusChart" wire:ignore></canvas>
            </div>
            <div class="w-full md:w-1/2 space-y-3">
                @foreach($statusData['labels'] as $index => $label)
                    <div class="flex items-center justify-between p-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="size-2.5 rounded-full" style="background-color: {{ ['#6366f1', '#3b82f6', '#f59e0b', '#ef4444', '#10b981', '#8b5cf6', '#ec4899'][$index] ?? '#ccc' }}"></span>
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                        </div>
                        <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400">{{ $statusData['values'][$index] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Device Type Distribution --}}
    <div class="premium-card p-8 rounded-2xl flex flex-col h-full">
        <div class="flex flex-col mb-8">
            <span class="stat-label">Inventory Mix</span>
            <flux:heading size="lg" class="tracking-tight">{{ __('Asset Categorization') }}</flux:heading>
        </div>
        
        <div class="flex-1 h-64">
            <canvas id="typeChart" wire:ignore></canvas>
        </div>

        <div class="mt-8 flex flex-wrap gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
            @foreach($typeData['labels'] as $index => $label)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}:</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-white">{{ $typeData['values'][$index] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    @script
    <script>
        // Set Chart.js defaults for premium look
        Chart.defaults.font.family = "'Inter', 'Instrument Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.plugins.tooltip.backgroundColor = '#111827';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.titleFont = { size: 12, weight: 'bold' };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };

        const statusCtx = document.getElementById('statusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @js($statusData['labels']),
                datasets: [{
                    data: @js($statusData['values']),
                    backgroundColor: [
                        '#6366f1', '#3b82f6', '#f59e0b', '#ef4444', '#10b981', '#8b5cf6', '#ec4899'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '82%',
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        const typeCtx = document.getElementById('typeChart');
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: @js($typeData['labels']),
                datasets: [{
                    label: 'Assets',
                    data: @js($typeData['values']),
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            padding: 10
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
    @endscript
</div>