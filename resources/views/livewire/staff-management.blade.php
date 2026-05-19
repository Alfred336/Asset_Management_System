<div class="space-y-8 p-4 lg:p-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <flux:heading size="xl" level="1" class="font-black tracking-tight">Workforce Registry</flux:heading>
            <flux:subheading>Personnel management across all business units.</flux:subheading>
        </div>

        @can('create-staff')
            <flux:button icon="plus" variant="primary" wire:click="creating" class="shadow-lg shadow-pink-500/25 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700">Onboard Personnel</flux:button>
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
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search personnel..." class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-pink-500" />
        </div>
        <div class="flex items-center gap-4">
            <flux:select wire:model.live="companyFilter" placeholder="All Companies" class="min-w-[160px]">
                <flux:select.option value="">All Companies</flux:select.option>
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:checkbox wire:model.live="showTrashed" label="Archive" />
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden premium-card">
        <flux:table :paginate="$staff">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'first_name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('first_name')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Full Identity</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Communication</flux:table.column>
                <flux:table.column class="hidden md:table-cell !bg-zinc-50/50 dark:!bg-zinc-800/20">Assigned To</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Status</flux:table.column>
                <flux:table.column align="end" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($staff as $member)
                    <flux:table.row :key="$member->id" class="group hover:bg-pink-50/30 dark:hover:bg-pink-900/10 transition-colors">
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">{{ $member->full_name }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">{{ $member->position ?: 'NO POSITION' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $member->email }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">{{ $member->phone ?: 'NO PHONE' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden md:table-cell">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $member->company->name ?? 'N/A' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $statusColor = match($member->status) {
                                    'active' => 'success',
                                    'inactive' => 'zinc',
                                    'on_leave' => 'warning',
                                    'terminated' => 'danger',
                                    default => 'zinc',
                                };
                            @endphp
                            @if($member->trashed())
                                <flux:badge color="danger" variant="solid" size="sm" class="font-bold uppercase text-[10px]">Archived</flux:badge>
                            @else
                                <flux:badge :color="$statusColor" variant="outline" size="sm" class="font-bold uppercase text-[10px]">
                                    {{ str_replace('_', ' ', $member->status) }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item icon="eye" wire:click="viewDetails({{ $member->id }})">Details</flux:menu.item>
                                    @if(!$member->trashed())
                                        @can('edit-staff')
                                            <flux:menu.item icon="pencil-square" wire:click="edit({{ $member->id }})">Edit</flux:menu.item>
                                        @endcan
                                        <flux:menu.separator />
                                        @can('delete-staff')
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $member->id }})">Delete</flux:menu.item>
                                        @endcan
                                    @else
                                        @can('restore-staff')
                                            <flux:menu.item icon="arrow-path" wire:click="restore({{ $member->id }})">Restore</flux:menu.item>
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
            <flux:heading size="lg">{{ $viewingStaff['full_name'] ?? 'Staff Details' }}</flux:heading>
            <flux:subheading>{{ $viewingStaff['position'] ?? 'View staff information.' }}</flux:subheading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @foreach([
                'Name' => $viewingStaff['full_name'] ?? null,
                'Email' => $viewingStaff['email'] ?? null,
                'Phone' => $viewingStaff['phone'] ?? null,
                'Company' => $viewingStaff['company'] ?? null,
                'Position' => $viewingStaff['position'] ?? null,
                'Hire Date' => $viewingStaff['hire_date'] ?? null,
                'Salary' => $viewingStaff['salary'] ?? null,
                'Employment Type' => $viewingStaff['employment_type'] ?? null,
                'Status' => $viewingStaff['status'] ?? null,
                'Assigned Devices' => $viewingStaff['devices'] ?? null,
                'Deleted At' => $viewingStaff['deleted_at'] ?? null,
            ] as $label => $value)
                <div>
                    <div class="text-xs font-medium text-zinc-500">{{ $label }}</div>
                    <div class="mt-1 text-zinc-900 dark:text-zinc-100">{{ filled($value) ? $value : 'N/A' }}</div>
                </div>
            @endforeach

            <div class="md:col-span-2">
                <div class="text-xs font-medium text-zinc-500">Notes</div>
                <div class="mt-1 whitespace-pre-line text-zinc-900 dark:text-zinc-100">{{ filled($viewingStaff['notes'] ?? null) ? $viewingStaff['notes'] : 'N/A' }}</div>
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
            <flux:heading size="lg">Onboard Personnel</flux:heading>
            <flux:subheading>Enter recruitment profile details.</flux:subheading>
        </div>

        <form wire:submit.prevent="create" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="company_id" label="Company" required placeholder="Select a company">
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="first_name" label="First Name" required />
                <flux:input wire:model="last_name" label="Last Name" required />
            </div>

            <flux:input type="email" wire:model="email" label="Email" required />
            <flux:input type="tel" wire:model="phone" label="Phone" />
            <flux:input wire:model="position" label="Position" />
            <flux:input type="date" wire:model="hire_date" label="Hire Date" />
            <flux:input type="number" step="0.01" wire:model="salary" label="Salary" min="0" />

            <flux:select wire:model="employment_type" label="Employment Type" required>
                <flux:select.option value="full_time">Full Time</flux:select.option>
                <flux:select.option value="part_time">Part Time</flux:select.option>
                <flux:select.option value="contract">Contract</flux:select.option>
                <flux:select.option value="intern">Intern</flux:select.option>
            </flux:select>

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
                <flux:select.option value="on_leave">On Leave</flux:select.option>
                <flux:select.option value="terminated">Terminated</flux:select.option>
            </flux:select>

            <div class="col-span-full">
                <flux:textarea wire:model="notes" label="Notes" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Add Staff Member</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Personnel</flux:heading>
            <flux:subheading>Update employment profile information.</flux:subheading>
        </div>

        <form wire:submit.prevent="update" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="company_id" label="Company" required placeholder="Select a company">
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="first_name" label="First Name" required />
                <flux:input wire:model="last_name" label="Last Name" required />
            </div>

            <flux:input type="email" wire:model="email" label="Email" required />
            <flux:input type="tel" wire:model="phone" label="Phone" />
            <flux:input wire:model="position" label="Position" />
            <flux:input type="date" wire:model="hire_date" label="Hire Date" />
            <flux:input type="number" step="0.01" wire:model="salary" label="Salary" min="0" />

            <flux:select wire:model="employment_type" label="Employment Type" required>
                <flux:select.option value="full_time">Full Time</flux:select.option>
                <flux:select.option value="part_time">Part Time</flux:select.option>
                <flux:select.option value="contract">Contract</flux:select.option>
                <flux:select.option value="intern">Intern</flux:select.option>
            </flux:select>

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
                <flux:select.option value="on_leave">On Leave</flux:select.option>
                <flux:select.option value="terminated">Terminated</flux:select.option>
            </flux:select>

            <div class="col-span-full">
                <flux:textarea wire:model="notes" label="Notes" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Staff Member</flux:button>
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
            <flux:subheading>Are you sure you want to delete this staff member? This action will move them to the archive.</flux:subheading>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" class="flex-1" wire:click="showDeleteConfirmation = false">Cancel</flux:button>
            <flux:button variant="danger" class="flex-1" wire:click="confirmDelete">Delete Personnel</flux:button>
        </div>
    </flux:modal>
</div>
