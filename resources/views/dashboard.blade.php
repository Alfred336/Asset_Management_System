<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <livewire:dashboard-stats />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <livewire:upcoming-reminders />
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <flux:heading size="lg" class="mb-4">{{ __('Quick Actions') }}</flux:heading>
                <div class="grid grid-cols-2 gap-4">
                    <flux:button :href="route('devices.index')" icon="computer-desktop" variant="ghost" class="justify-start">Add Device</flux:button>
                    <flux:button :href="route('staff.index')" icon="users" variant="ghost" class="justify-start">Add Staff</flux:button>
                    <flux:button :href="route('companies.index')" icon="home" variant="ghost" class="justify-start">Add Company</flux:button>
                    <flux:button :href="route('users.index')" icon="user-group" variant="ghost" class="justify-start">Manage Users</flux:button>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
