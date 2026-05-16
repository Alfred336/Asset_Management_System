<?php

use Livewire\Volt\Component;
use App\Models\Device;
use App\Models\Staff;
use App\Models\Company;

new class extends Component
{
    public function with()
    {
        return [
            'deviceCount' => Device::count(),
            'staffCount' => Staff::count(),
            'companyCount' => Company::count(),
            'expiringSoon' => Device::where('warranty_expiry', '<=', now()->addDays(30))
                ->where('warranty_expiry', '>=', now())
                ->count(),
        ];
    }
};
?>

<div class="grid auto-rows-min gap-4 md:grid-cols-3">
    <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
        <flux:heading size="sm" class="text-zinc-500">{{ __('Total Devices') }}</flux:heading>
        <flux:text size="xl" class="font-semibold">{{ $deviceCount }}</flux:text>
    </div>
    <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
        <flux:heading size="sm" class="text-zinc-500">{{ __('Total Staff') }}</flux:heading>
        <flux:text size="xl" class="font-semibold">{{ $staffCount }}</flux:text>
    </div>
    <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
        <flux:heading size="sm" class="text-zinc-500">{{ __('Expiring Warranties') }}</flux:heading>
        <div class="flex items-baseline gap-2">
            <flux:text size="xl" class="font-semibold text-amber-600">{{ $expiringSoon }}</flux:text>
            <flux:text size="xs" class="text-zinc-500">{{ __('(next 30 days)') }}</flux:text>
        </div>
    </div>
</div>
