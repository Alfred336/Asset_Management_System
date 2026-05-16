<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Role Management</flux:heading>
            <flux:subheading>Manage roles and assign permissions across the system.</flux:subheading>
        </div>

        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('edit-roles'))
            <flux:button icon="plus" variant="primary" wire:click="creating">Add Role</flux:button>
        @endif
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="flex-1 w-full">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search roles by name..." />
        </div>
    </div>

    <flux:table :paginate="$roles">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')">Role</flux:table.column>
            <flux:table.column>Permissions</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($roles as $role)
                <flux:table.row :key="$role->id">
                    <flux:table.cell class="font-medium">{{ $role->name }}</flux:table.cell>
                    <flux:table.cell>
                        @if($role->permissions->isEmpty())
                            <flux:text color="zinc" size="sm">No permissions assigned</flux:text>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($role->permissions as $permission)
                                    <flux:badge color="zinc" size="sm">{{ $permission->name }}</flux:badge>
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

    <flux:modal wire:model="showCreateModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Add New Role</flux:heading>
            <flux:subheading>Create a role and assign permissions.</flux:subheading>
        </div>

        <form wire:submit.prevent="create" class="space-y-6">
            <flux:input wire:model="name" label="Role Name" required />

            <div class="space-y-3">
                <flux:heading size="sm">Permissions</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
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
        <div>
            <flux:heading size="lg">Edit Role</flux:heading>
            <flux:subheading>Update the role name and permission assignments.</flux:subheading>
        </div>

        <form wire:submit.prevent="update" class="space-y-6">
            <flux:input wire:model="name" label="Role Name" required />

            <div class="space-y-3">
                <flux:heading size="sm">Permissions</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
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
