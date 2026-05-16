<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">User Management</flux:heading>
            <flux:subheading>Manage system users and their access levels.</flux:subheading>
        </div>

        @can('create-user')
            <flux:button icon="plus" variant="primary" wire:click="creating">Add User</flux:button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="flex-1 w-full">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search users by name, email..." />
        </div>
        <flux:checkbox wire:model.live="showTrashed" label="Show deleted users" />
    </div>

    <!-- Table -->
    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')">Email</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @if($user->trashed())
                            <flux:badge color="danger">Deleted</flux:badge>
                        @elseif($user->email_verified_at)
                            <flux:badge color="success">Active</flux:badge>
                        @else
                            <flux:badge color="warning">Unverified</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button.group>
                            @if(!$user->trashed())
                                @can('edit-user')
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $user->id }})" />
                                @endcan
                                @can('delete-user')
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $user->id }})" />
                                @endcan
                            @else
                                @can('restore-user')
                                    <flux:button variant="ghost" size="sm" icon="arrow-path" wire:click="restore({{ $user->id }})" />
                                @endcan
                                @can('delete-user')
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="forceDelete({{ $user->id }})" />
                                @endcan
                            @endif
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Modal -->
    <flux:modal wire:model="showCreateModal" class="space-y-6">
        <div>
            <flux:heading size="lg">Add New User</flux:heading>
            <flux:subheading>Create a new system user.</flux:subheading>
        </div>

        <form wire:submit.prevent="create" class="space-y-6">
            <flux:input wire:model="name" label="Name" required />
            <flux:input type="email" wire:model="email" label="Email" required />
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="password" wire:model="password" label="Password" required />
                <flux:input type="password" wire:model="password_confirmation" label="Confirm Password" required />
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Add User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" class="space-y-6">
        <div>
            <flux:heading size="lg">Edit User</flux:heading>
            <flux:subheading>Update user details.</flux:subheading>
        </div>

        <form wire:submit.prevent="update" class="space-y-6">
            <flux:input wire:model="name" label="Name" required />
            <flux:input type="email" wire:model="email" label="Email" required />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update User</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
