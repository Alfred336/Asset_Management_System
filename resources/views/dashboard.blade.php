<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <livewire:notification-bell />
                <span class="text-xs text-slate-400 dark:text-slate-500">{{ now()->format('D, d M Y') }}</span>
            </div>
        </div>

        {{-- Stats --}}
        <livewire:dashboard-stats />

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Warranty Expiries (2/3 width) --}}
            <div class="lg:col-span-2">
                <livewire:upcoming-reminders />
            </div>

            {{-- Quick Actions (1/3 width) --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 uppercase tracking-wide">Quick Actions</h2>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('devices.index') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-slate-700 dark:text-slate-300 text-sm font-medium transition group">
                        <flux:icon name="computer-desktop" class="size-4 text-slate-400 group-hover:text-rose-500 transition" />
                        Devices
                    </a>
                    <a href="{{ route('staff.index') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-slate-700 dark:text-slate-300 text-sm font-medium transition group">
                        <flux:icon name="users" class="size-4 text-slate-400 group-hover:text-rose-500 transition" />
                        Staff
                    </a>
                    <a href="{{ route('companies.index') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-slate-700 dark:text-slate-300 text-sm font-medium transition group">
                        <flux:icon name="building-office" class="size-4 text-slate-400 group-hover:text-rose-500 transition" />
                        Companies
                    </a>
                    <a href="{{ route('users.index') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-slate-700 dark:text-slate-300 text-sm font-medium transition group">
                        <flux:icon name="user-group" class="size-4 text-slate-400 group-hover:text-rose-500 transition" />
                        Users
                    </a>
                    <a href="{{ route('roles.index') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-slate-700 dark:text-slate-300 text-sm font-medium transition group">
                        <flux:icon name="shield-check" class="size-4 text-slate-400 group-hover:text-rose-500 transition" />
                        Roles
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts::app>
