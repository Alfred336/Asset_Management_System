<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    {{-- Total Devices --}}
    <div class="premium-card p-6 rounded-2xl group overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <flux:icon name="computer-desktop" class="size-16 text-brand-600" />
        </div>
        <div class="flex flex-col gap-1">
            <span class="stat-label">Inventory Assets</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $deviceCount }}</span>
                <span class="text-xs font-medium text-emerald-600 flex items-center gap-0.5">
                    <flux:icon name="arrow-trending-up" class="size-3" />
                    12%
                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Active monitored devices</p>
        </div>
    </div>

    {{-- Total Staff --}}
    <div class="premium-card p-6 rounded-2xl group overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <flux:icon name="users" class="size-16 text-indigo-600" />
        </div>
        <div class="flex flex-col gap-1">
            <span class="stat-label">Active Staff</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $staffCount }}</span>
                <span class="text-xs font-medium text-emerald-600 flex items-center gap-0.5">
                    <flux:icon name="arrow-trending-up" class="size-3" />
                    4%
                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Verified employees</p>
        </div>
    </div>

    {{-- Companies --}}
    <div class="premium-card p-6 rounded-2xl group overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <flux:icon name="building-office" class="size-16 text-purple-600" />
        </div>
        <div class="flex flex-col gap-1">
            <span class="stat-label">Client Entities</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $companyCount }}</span>
                <span class="text-xs font-medium text-zinc-400">Stable</span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Managed business units</p>
        </div>
    </div>

    {{-- Expiring Soon --}}
    <div class="premium-card p-6 rounded-2xl group overflow-hidden relative border-amber-200/50 dark:border-amber-900/30">
        <div class="absolute inset-0 bg-amber-500/[0.02] pointer-events-none"></div>
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <flux:icon name="clock" class="size-16 text-amber-600" />
        </div>
        <div class="flex flex-col gap-1">
            <span class="stat-label text-amber-600 dark:text-amber-400">Critical Expiries</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-amber-600 dark:text-amber-500">{{ $expiringSoon }}</span>
                <span class="text-xs font-medium text-amber-600/70">Action Required</span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Next 30 calendar days</p>
        </div>
    </div>
</div>