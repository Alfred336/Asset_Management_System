<?php

use Livewire\Volt\Component;
use App\Models\Device;

new class extends Component
{
    public function with()
    {
        return [
            'devices' => Device::with('company')
                ->where('warranty_expiry', '<=', now()->addDays(60))
                ->where('warranty_expiry', '>=', now())
                ->orderBy('warranty_expiry', 'asc')
                ->take(5)
                ->get(),
        ];
    }
};
?>

<div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
    <flux:heading size="lg" class="mb-4">{{ __('Upcoming Warranty Expiries') }}</flux:heading>

    @if($devices->isEmpty())
        <flux:text color="zinc">{{ __('No upcoming expiries found.') }}</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Device') }}</flux:table.column>
                <flux:table.column>{{ __('Company') }}</flux:table.column>
                <flux:table.column>{{ __('Expires') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($devices as $device)
                    <flux:table.row :key="$device->id">
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-medium text-sm">{{ $device->asset_tag }}</span>
                                <span class="text-xs text-zinc-500">{{ $device->model }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $device->company->name }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $days = now()->diffInDays($device->warranty_expiry);
                                $color = $days < 30 ? 'warning' : 'zinc';
                            @endphp
                            <flux:badge :color="$color" size="sm">
                                {{ $device->warranty_expiry->format('Y-m-d') }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        
        <div class="mt-4">
            <flux:link :href="route('devices.index')" class="text-sm">{{ __('View all devices') }}</flux:link>
        </div>
    @endif
</div>
