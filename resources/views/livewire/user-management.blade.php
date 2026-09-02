<div class="space-y-8 p-4 lg:p-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="space-y-1">
            <flux:heading size="xl" level="1" class="font-black tracking-tight">System Users</flux:heading>
            <flux:subheading>Manage access, roles, and user profiles.</flux:subheading>
        </div>
        @can('create-user')
            <flux:button icon="plus" variant="primary" wire:click="creating" class="shadow-sm">Provision User</flux:button>
        @endcan
    </div>

    @if (session()->has('message'))
        <flux:badge color="success" class="w-full justify-center py-3 rounded-xl border-emerald-100 dark:border-emerald-900/30">{{ session('message') }}</flux:badge>
    @endif

    @if (session()->has('error'))
        <flux:badge color="danger" class="w-full justify-center py-2">{{ session('error') }}</flux:badge>
    @endif

    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search system users..." class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl" />
        </div>
        <flux:checkbox wire:model.live="showTrashed" label="Archive" />
    </div>

    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden premium-card">
        <flux:table :paginate="$users">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Identity</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Contact</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Role</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Assigned Companies</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Status</flux:table.column>
                <flux:table.column align="end" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($users as $user)
                    <flux:table.row :key="$user->id" class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $user->name }}</flux:table.cell>
                        <flux:table.cell class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>
                            @if($user->roles->isNotEmpty())
                                <flux:badge color="brand" variant="outline" size="sm" class="font-bold uppercase text-[10px]">{{ $user->roles->first()->name }}</flux:badge>
                            @else
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($user->canAccessAllCompanies())
                                <flux:badge color="purple" variant="outline" size="sm" class="font-bold uppercase text-[10px]">All Companies (Admin)</flux:badge>
                            @elseif($user->companies->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->companies as $comp)
                                        <flux:badge color="zinc" variant="subtle" size="sm" class="text-[10px]">{{ $comp->name }}</flux:badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400">None</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($user->trashed())
                                <flux:badge color="danger" variant="solid" size="sm" class="font-bold uppercase text-[10px]">Archived</flux:badge>
                            @elseif($user->email_verified_at)
                                <flux:badge color="success" variant="outline" size="sm" class="font-bold uppercase text-[10px]">Active</flux:badge>
                            @else
                                <flux:badge color="warning" variant="outline" size="sm" class="font-bold uppercase text-[10px]">Pending</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    @if(!$user->trashed())
                                        @can('edit-user')
                                            <flux:menu.item icon="pencil-square" wire:click="edit({{ $user->id }})">Edit</flux:menu.item>
                                        @endcan
                                        <flux:menu.separator />
                                        @can('delete-user')
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $user->id }})">Delete</flux:menu.item>
                                        @endcan
                                    @else
                                        @can('restore-user')
                                            <flux:menu.item icon="arrow-path" wire:click="restore({{ $user->id }})">Restore</flux:menu.item>
                                        @endcan
                                        @can('delete-user')
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $user->id }})">Delete</flux:menu.item>
                                        @endcan
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Create Modal -->
    <flux:modal wire:model="showCreateModal" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Provision User</flux:heading>
                <flux:subheading>Create a new system user account.</flux:subheading>
            </div>
        </div>
        <form wire:submit.prevent="create" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" label="Name" required />
                <flux:input type="email" wire:model="email" label="Email" required />
                <div class="grid grid-cols-2 gap-4">
                    <flux:input type="password" wire:model="password" label="Password" required />
                    <flux:input type="password" wire:model="password_confirmation" label="Confirm Password" required />
                </div>
                <flux:select wire:model="selectedRole" label="Role">
                    <flux:select.option value="">— No Role —</flux:select.option>
                    @foreach($roles as $role)
                        <flux:select.option value="{{ $role->name }}">{{ $role->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Assigned Companies / Workplaces</label>
                    <span class="text-[10px] text-zinc-400 uppercase">Multi-select</span>
                </div>
                <div class="max-h-40 overflow-y-auto space-y-2 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                    @forelse($companies as $company)
                        <label class="flex items-center gap-2 text-sm text-zinc-800 dark:text-zinc-200 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800 p-1 rounded transition-colors">
                            <input type="checkbox" value="{{ $company->id }}" wire:model="selectedCompanies" class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span class="truncate">{{ $company->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-zinc-500">No active companies available.</p>
                    @endforelse
                </div>
                <p class="text-xs text-zinc-500">Users can only access data belonging to selected companies. Super Admin / System Admin users retain access to all companies.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Provision User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Edit User</flux:heading>
                <flux:subheading>Update system user account details.</flux:subheading>
            </div>
        </div>
        <form wire:submit.prevent="update" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" label="Name" required />
                <flux:input type="email" wire:model="email" label="Email" required />
                <flux:select wire:model="selectedRole" label="Role">
                    <flux:select.option value="">— No Role —</flux:select.option>
                    @foreach($roles as $role)
                        <flux:select.option value="{{ $role->name }}">{{ $role->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="status" label="Status">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Assigned Companies / Workplaces</label>
                    <span class="text-[10px] text-zinc-400 uppercase">Multi-select</span>
                </div>
                <div class="max-h-40 overflow-y-auto space-y-2 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                    @forelse($companies as $company)
                        <label class="flex items-center gap-2 text-sm text-zinc-800 dark:text-zinc-200 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800 p-1 rounded transition-colors">
                            <input type="checkbox" value="{{ $company->id }}" wire:model="selectedCompanies" class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span class="truncate">{{ $company->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-zinc-500">No active companies available.</p>
                    @endforelse
                </div>
                <p class="text-xs text-zinc-500">Users can only access data belonging to selected companies. Super Admin / System Admin users retain access to all companies.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteConfirmation" class="max-w-sm space-y-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <flux:icon name="trash" class="size-6 text-red-600 dark:text-red-400" />
            </div>
            <flux:heading size="lg">Confirm Permanent Deletion</flux:heading>
            <flux:subheading>This action will permanently remove the user and cannot be undone.</flux:subheading>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" class="flex-1" wire:click="showDeleteConfirmation = false">Cancel</flux:button>
            <flux:button variant="danger" class="flex-1" wire:click="confirmDelete">Delete User</flux:button>
        </div>
    </flux:modal>
</div>
