<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-6 lg:p-10 bg-zinc-50/50 dark:bg-zinc-950/20">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[10px] font-bold uppercase tracking-wider">Enterprise Console</span>
                    <flux:breadcrumbs class="text-zinc-400">
                        <flux:breadcrumbs.item>System</flux:breadcrumbs.item>
                        <flux:breadcrumbs.item>Overview</flux:breadcrumbs.item>
                    </flux:breadcrumbs>
                </div>
                <flux:heading size="xl" class="font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Infrastructure Overview') }}</flux:heading>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-sm group focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <flux:icon name="magnifying-glass" class="size-4 text-zinc-400 group-hover:text-zinc-500 transition-colors" />
                    <input type="text" placeholder="Global search..." class="bg-transparent border-none text-sm focus:outline-none w-40 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400">
                    <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 border border-zinc-200 dark:border-zinc-700 rounded text-[10px] text-zinc-400 font-sans">⌘K</kbd>
                </div>
                <livewire:notification-bell />
            </div>
        </div>

        <div class="space-y-8">
            {{-- Stats Layer --}}
            <livewire:dashboard-stats />

            {{-- Analytics Layer --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-500/10 rounded-lg">
                            <flux:icon name="chart-bar" class="size-5 text-brand-600" />
                        </div>
                        <flux:heading size="lg" class="tracking-tight">{{ __('Operational Analytics') }}</flux:heading>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="arrow-path" class="text-zinc-500 hover:text-brand-600 transition-colors">Refresh Data</flux:button>
                </div>
                <div class="premium-card rounded-2xl p-6">
                    <livewire:dashboard-charts />
                </div>
            </div>

            {{-- Activity & Quick Links Layer --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-500/10 rounded-lg">
                            <flux:icon name="shield-check" class="size-5 text-amber-600" />
                        </div>
                        <flux:heading size="lg" class="tracking-tight">{{ __('Lifecycle Monitoring') }}</flux:heading>
                    </div>
                    <div class="premium-card rounded-2xl p-6">
                        <livewire:upcoming-reminders />
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-500/10 rounded-lg">
                            <flux:icon name="bolt" class="size-5 text-indigo-600" />
                        </div>
                        <flux:heading size="lg" class="tracking-tight">{{ __('Fast Access') }}</flux:heading>
                    </div>
                    <div class="premium-card rounded-2xl p-6 flex flex-col gap-4">
                        <a href="{{ route('devices.index') }}" wire:navigate
                            class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                    <flux:icon name="computer-desktop" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                </div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Assets Inventory</span>
                            </div>
                            <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                        </a>
                        <a href="{{ route('staff.index') }}" wire:navigate
                            class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                    <flux:icon name="users" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                </div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Staff Directory</span>
                            </div>
                            <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                        </a>
                        <a href="{{ route('companies.index') }}" wire:navigate
                            class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                    <flux:icon name="building-office" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                </div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Enterprise Units</span>
                            </div>
                            <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                        </a>
                        
                        <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800">
                            <flux:button href="{{ route('reports.weekly-download') }}" variant="primary" class="w-full shadow-sm" icon="arrow-down-tray">Download Global Report</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
