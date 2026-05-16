<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Staff Members</flux:heading>
            <flux:subheading>Manage employees across different companies.</flux:subheading>
        </div>

        @can('create-staff')
            <flux:button icon="plus" variant="primary" wire:click="creating">Add Staff</flux:button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search staff by name, email..." />
        </div>
        <div class="flex gap-4">
            <flux:select wire:model.live="companyFilter" placeholder="All Companies">
                <flux:select.option value="">All Companies</flux:select.option>
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <flux:table :paginate="$staff">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortField === 'first_name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('first_name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')">Email</flux:table.column>
            <flux:table.column>Company</flux:table.column>
            <flux:table.column>Position</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($staff as $member)
                <flux:table.row :key="$member->id">
                    <flux:table.cell class="font-medium">{{ $member->full_name }}</flux:table.cell>
                    <flux:table.cell>{{ $member->email }}</flux:table.cell>
                    <flux:table.cell>{{ $member->company->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $member->position ?? 'N/A' }}</flux:table.cell>
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
                        <flux:badge :color="$statusColor">
                            {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button.group>
                            @can('edit-staff')
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $member->id }})" />
                            @endcan
                            @can('delete-staff')
                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $member->id }})" />
                            @endcan
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Modal -->
    <flux:modal wire:model="showCreateModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Add Staff Member</flux:heading>
            <flux:subheading>Enter the details for the new employee.</flux:subheading>
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
            <flux:heading size="lg">Edit Staff Member</flux:heading>
            <flux:subheading>Update the details for this employee.</flux:subheading>
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
</div>
