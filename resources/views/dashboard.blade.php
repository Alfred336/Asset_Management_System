<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-10 p-8 lg:p-12 bg-zinc-50/50 dark:bg-zinc-950/20">

        {{-- Top Bar & Navigation --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[10px] font-bold uppercase tracking-wider">Enterprise</span>
                    <flux:breadcrumbs class="text-zinc-400">
                        <flux:breadcrumbs.item>Global</flux:breadcrumbs.item>
                        <flux:breadcrumbs.item>Overview</flux:breadcrumbs.item>
                    </flux:breadcrumbs>
                </div>
                <flux:heading size="xl" class="font-black tracking-tight text-zinc-900 dark:text-white">{{ __('System Overview') }}</flux:heading>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm group focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <flux:icon name="magnifying-glass" class="size-4 text-zinc-400 group-hover:text-zinc-500 transition-colors" />
                    <input type="text" placeholder="Search infrastructure..." class="bg-transparent border-none text-sm focus:outline-none w-48 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400">
                    <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 border border-zinc-200 dark:border-zinc-700 rounded text-[10px] text-zinc-400 font-sans">⌘K</kbd>
                </div>
                <livewire:notification-bell />
            </div>
        </div>

        <div class="space-y-10">
            {{-- Stats Layer --}}
            <livewire:dashboard-stats />

            {{-- Analytics Layer --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <flux:icon name="chart-bar" class="size-5 text-brand-600" />
                        <flux:heading size="lg" class="tracking-tight">{{ __('Infrastructure Analytics') }}</flux:heading>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="arrow-path" class="text-zinc-500">Refresh Data</flux:button>
                </div>
                <livewire:dashboard-charts />
            </div>

            {{-- Activity & Reminders Layer --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center gap-3">
                        <flux:icon name="shield-check" class="size-5 text-amber-600" />
                        <flux:heading size="lg" class="tracking-tight">{{ __('Lifecycle Monitoring') }}</flux:heading>
                    </div>
                    <livewire:upcoming-reminders />
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <flux:icon name="bolt" class="size-5 text-indigo-600" />
                        <flux:heading size="lg" class="tracking-tight">{{ __('Quick Control') }}</flux:heading>
                    </div>
                    <div class="premium-card rounded-2xl p-6 overflow-hidden relative">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 size-32 bg-brand-500/5 rounded-full blur-3xl"></div>
                        <div class="flex flex-col gap-3 relative">
                            <a href="{{ route('devices.index') }}" wire:navigate
                                class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                        <flux:icon name="computer-desktop" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                    </div>
                                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Infrastructure</span>
                                </div>
                                <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                            </a>
                            <a href="{{ route('staff.index') }}" wire:navigate
                                class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                        <flux:icon name="users" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                    </div>
                                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Workforce</span>
                                </div>
                                <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                            </a>
                            <a href="{{ route('companies.index') }}" wire:navigate
                                class="flex items-center justify-between group p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all border border-transparent hover:border-zinc-100 dark:hover:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 transition-colors">
                                        <flux:icon name="building-office" class="size-4 text-zinc-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" />
                                    </div>
                                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Enterprise Units</span>
                                </div>
                                <flux:icon name="chevron-right" class="size-3 text-zinc-300 group-hover:text-zinc-500 transition-all group-hover:translate-x-0.5" />
                            </a>
                            
                            <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800">
                                <flux:button variant="primary" class="w-full shadow-lg shadow-brand-500/20" icon="plus">Generate Global Report</flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
