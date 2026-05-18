<div class="premium-card p-8 rounded-2xl overflow-hidden relative">
    <div class="absolute top-0 right-0 -mt-10 -mr-10 size-64 bg-amber-500/[0.03] rounded-full blur-3xl"></div>
    
    <div class="flex items-center justify-between mb-8 relative">
        <div class="flex flex-col">
            <span class="stat-label text-amber-600 dark:text-amber-400">Risk Assessment</span>
            <flux:heading size="lg" class="tracking-tight">{{ __('Upcoming Warranty Expiries') }}</flux:heading>
        </div>
        <flux:badge color="amber" variant="solid" class="shadow-sm shadow-amber-500/20">Action Required</flux:badge>
    </div>

    @if($devices->isEmpty())
        <div class="py-12 flex flex-col items-center justify-center text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-2xl">
            <flux:icon name="shield-check" class="size-10 text-zinc-300 mb-3" />
            <flux:text color="zinc" class="font-medium text-sm">{{ __('No upcoming expiries found.') }}</flux:text>
            <flux:text color="zinc" size="xs">{{ __('All assets are currently within their warranty lifecycle.') }}</flux:text>
        </div>
    @else
        <div class="relative overflow-hidden rounded-xl border border-zinc-100 dark:border-zinc-800/50">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">{{ __('Device Identity') }}</flux:table.column>
                    <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">{{ __('Assigned Entity') }}</flux:table.column>
                    <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20" align="end">{{ __('Expiration Date') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($devices as $device)
                        <flux:table.row :key="$device->id" class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-zinc-900 dark:text-zinc-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $device->asset_tag }}</span>
                                    <span class="text-xs text-zinc-500">{{ $device->model }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $device->company->name }}</flux:table.cell>
                            <flux:table.cell align="end">
                                @php
                                    $days = now()->diffInDays($device->warranty_expiry);
                                    $color = $days < 30 ? 'warning' : 'zinc';
                                    $variant = $days < 30 ? 'solid' : 'outline';
                                @endphp
                                <div class="flex flex-col items-end gap-1">
                                    <flux:badge :color="$color" :variant="$variant" size="sm" class="font-bold tracking-tight">
                                        {{ $device->warranty_expiry->format('M d, Y') }}
                                    </flux:badge>
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">
                                        {{ $days }} {{ __('days remaining') }}
                                    </span>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="mt-8 flex items-center justify-between pt-6 border-t border-zinc-100 dark:border-zinc-800 relative">
            <flux:text size="xs" color="zinc" class="italic">{{ __('Showing most critical lifecycle risks.') }}</flux:text>
            <flux:link :href="route('devices.index')" class="text-xs font-bold uppercase tracking-widest text-brand-600 hover:text-brand-700 transition-colors">{{ __('Manage Inventory') }} →</flux:link>
        </div>
    @endif
</div>