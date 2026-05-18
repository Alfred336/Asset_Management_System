<div class="space-y-8 p-4 lg:p-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <flux:heading size="xl" level="1" class="font-black tracking-tight">Client Entities</flux:heading>
            <flux:subheading>Registry of managed business organizations.</flux:subheading>
        </div>

        @can('create-companies')
            <flux:button icon="plus" variant="primary" wire:click="creating" class="shadow-lg shadow-brand-500/20">Register Entity</flux:button>
        @endcan
    </div>

    @if (session()->has('message'))
        <flux:badge color="success" class="w-full justify-center py-3 rounded-xl border-emerald-100 dark:border-emerald-900/30">{{ session('message') }}</flux:badge>
    @endif

    @if (session()->has('error'))
        <flux:badge color="danger" class="w-full justify-center py-2">{{ session('error') }}</flux:badge>
    @endif

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search companies..." class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl" />
        </div>
        <flux:checkbox wire:model.live="showTrashed" label="Archive" />
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <flux:table :paginate="$companies">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Organization Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Primary Contact</flux:table.column>
                <flux:table.column class="hidden md:table-cell !bg-zinc-50/50 dark:!bg-zinc-800/20">Status</flux:table.column>
                <flux:table.column align="end" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($companies as $company)
                    <flux:table.row :key="$company->id" class="group hover:bg-zinc-50/30 dark:hover:bg-zinc-800/30 transition-colors">
                        <flux:table.cell>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $company->name }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $company->email }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">{{ $company->phone ?? 'NO PHONE' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden md:table-cell">
                            @if($company->trashed())
                                <flux:badge color="danger" variant="solid" size="sm" class="font-bold uppercase text-[10px]">Archived</flux:badge>
                            @else
                                @php
                                    $statusColor = match($company->status) {
                                        'active' => 'success',
                                        'inactive' => 'zinc',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge :color="$statusColor" variant="outline" size="sm" class="font-bold uppercase text-[10px]">
                                    {{ $company->status }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item icon="eye" wire:click="viewDetails({{ $company->id }})">Details</flux:menu.item>
                                    @if(!$company->trashed())
                                        @can('edit-companies')
                                            <flux:menu.item icon="pencil-square" wire:click="edit({{ $company->id }})">Edit</flux:menu.item>
                                        @endcan
                                        <flux:menu.separator />
                                        @can('delete-companies')
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $company->id }})">Delete</flux:menu.item>
                                        @endcan
                                    @else
                                        @can('restore-companies')
                                            <flux:menu.item icon="arrow-path" wire:click="restore({{ $company->id }})">Restore</flux:menu.item>
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

    <!-- Details Modal -->
    <flux:modal wire:model="showDetailsModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $viewingCompany['name'] ?? 'Company Details' }}</flux:heading>
            <flux:subheading>{{ $viewingCompany['email'] ?? 'View company information.' }}</flux:subheading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @foreach([
                'Name' => $viewingCompany['name'] ?? null,
                'Email' => $viewingCompany['email'] ?? null,
                'Phone' => $viewingCompany['phone'] ?? null,
                'Website' => $viewingCompany['website'] ?? null,
                'Tax ID' => $viewingCompany['tax_id'] ?? null,
                'Status' => $viewingCompany['status'] ?? null,
                'Staff Count' => $viewingCompany['staff_count'] ?? null,
                'Device Count' => $viewingCompany['device_count'] ?? null,
                'Deleted At' => $viewingCompany['deleted_at'] ?? null,
            ] as $label => $value)
                <div>
                    <div class="text-xs font-medium text-zinc-500">{{ $label }}</div>
                    <div class="mt-1 text-zinc-900 dark:text-zinc-100">{{ filled($value) ? $value : 'N/A' }}</div>
                </div>
            @endforeach

            <div class="md:col-span-2">
                <div class="text-xs font-medium text-zinc-500">Address</div>
                <div class="mt-1 whitespace-pre-line text-zinc-900 dark:text-zinc-100">{{ filled($viewingCompany['address'] ?? null) ? $viewingCompany['address'] : 'N/A' }}</div>
            </div>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:button variant="primary" wire:click="showDetailsModal = false">Close</flux:button>
        </div>
    </flux:modal>

    <!-- Create Modal -->
    <flux:modal wire:model="showCreateModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Register Entity</flux:heading>
            <flux:subheading>Enter organization profile details.</flux:subheading>
        </div>

        <form wire:submit.prevent="create" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="name" label="Company Name" required />
            <flux:input type="email" wire:model="email" label="Email" required />
            <flux:input wire:model="phone" label="Phone" />
            <flux:input wire:model="website" label="Website" placeholder="https://..." />
            <flux:input wire:model="tax_id" label="Tax ID / Registration Number" />

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
            </flux:select>

            <div class="col-span-full">
                <flux:textarea wire:model="address" label="Address" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Add Company</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Entity</flux:heading>
            <flux:subheading>Update organization profile information.</flux:subheading>
        </div>

        <form wire:submit.prevent="update" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="name" label="Company Name" required />
            <flux:input type="email" wire:model="email" label="Email" required />
            <flux:input wire:model="phone" label="Phone" />
            <flux:input wire:model="website" label="Website" />
            <flux:input wire:model="tax_id" label="Tax ID / Registration Number" />

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
            </flux:select>

            <div class="col-span-full">
                <flux:textarea wire:model="address" label="Address" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Company</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteConfirmation" class="max-w-sm space-y-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <flux:icon name="trash" class="size-6 text-red-600 dark:text-red-400" />
            </div>
            <flux:heading size="lg">Confirm Deletion</flux:heading>
            <flux:subheading>Are you sure you want to delete this company? This action will move it to the archive.</flux:subheading>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" class="flex-1" wire:click="showDeleteConfirmation = false">Cancel</flux:button>
            <flux:button variant="danger" class="flex-1" wire:click="confirmDelete">Delete Company</flux:button>
        </div>
    </flux:modal>
</div>
