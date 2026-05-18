<?php

namespace App\Livewire;

use App\Models\Device;
use Livewire\Component;

class DashboardCharts extends Component
{
    public array $statusData = [];

    public array $typeData = [];

    public function mount(): void
    {
        $statusCounts = Device::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->pluck('count', 'status')
            ->all();

        $typeCounts = Device::selectRaw('device_type, count(*) as count')
            ->groupBy('device_type')
            ->orderBy('device_type')
            ->get()
            ->pluck('count', 'device_type')
            ->all();

        $this->statusData = [
            'labels' => array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), array_keys($statusCounts)),
            'values' => array_values($statusCounts),
        ];

        $this->typeData = [
            'labels' => array_keys($typeCounts),
            'values' => array_values($typeCounts),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-charts');
    }
}
