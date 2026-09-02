<div class="space-y-8 p-4 lg:p-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="space-y-1">
            <flux:heading size="xl" level="1" class="font-black tracking-tight">Access Control</flux:heading>
            <flux:subheading>Manage roles and assign permissions across the system.</flux:subheading>
        </div>

        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('edit-roles'))
            <flux:button icon="plus" variant="primary" wire:click="creating" class="shadow-sm">Add Role</flux:button>
        @endif
    </div>

    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search roles by name..." class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl" />
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden premium-card">
        <flux:table :paginate="$roles">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Role</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Permissions</flux:table.column>
                <flux:table.column align="end" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($roles as $role)
                    <flux:table.row :key="$role->id" class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $role->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if($role->permissions->isEmpty())
                                <flux:text variant="subtle" size="sm">No permissions assigned</flux:text>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($role->permissions as $permission)
                                        <flux:badge color="zinc" size="sm" class="font-medium text-[10px]">{{ $permission->name }}</flux:badge>
                                    @endforeach
                                </div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button.group>
                                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('edit-roles'))
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $role->id }})" />
                                    @if($role->name !== 'Super Admin')
                                        <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $role->id }})" />
                                    @endif
                                @endif
                            </flux:button.group>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal wire:model="showCreateModal" variant="wide" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Add New Role</flux:heading>
                <flux:subheading>Create a role and assign permissions.</flux:subheading>
            </div>
        </div>

        <form wire:submit.prevent="create" class="space-y-6">
            <flux:input wire:model="name" label="Role Name" required />

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">Permissions</flux:heading>
                    <span class="text-[10px] text-zinc-400 uppercase">Select all that apply</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    @foreach ($permissions as $permission)
                        <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->name }}" label="{{ $permission->name }}" />
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Add Role</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showEditModal" variant="wide" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Edit Role</flux:heading>
                <flux:subheading>Update the role name and permission assignments.</flux:subheading>
            </div>
        </div>

        <form wire:submit.prevent="update" class="space-y-6">
            <flux:input wire:model="name" label="Role Name" required />

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">Permissions</flux:heading>
                    <span class="text-[10px] text-zinc-400 uppercase">Select all that apply</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    @foreach ($permissions as $permission)
                        <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->name }}" label="{{ $permission->name }}" />
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Role</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
