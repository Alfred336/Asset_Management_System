<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Companies</flux:heading>
            <flux:subheading>Manage client companies and their details.</flux:subheading>
        </div>

        @can('create-companies')
            <flux:button icon="plus" variant="primary" wire:click="creating">Add Company</flux:button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search companies by name, email..." />
        </div>
    </div>

    <!-- Table -->
    <flux:table :paginate="$companies">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('email')">Email</flux:table.column>
            <flux:table.column>Phone</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($companies as $company)
                <flux:table.row :key="$company->id">
                    <flux:table.cell class="font-medium">{{ $company->name }}</flux:table.cell>
                    <flux:table.cell>{{ $company->email }}</flux:table.cell>
                    <flux:table.cell>{{ $company->phone ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            $statusColor = match($company->status) {
                                'active' => 'success',
                                'inactive' => 'zinc',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge :color="$statusColor">
                            {{ ucfirst($company->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button.group>
                            @can('edit-companies')
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $company->id }})" />
                            @endcan
                            @can('delete-companies')
                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $company->id }})" />
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
            <flux:heading size="lg">Add New Company</flux:heading>
            <flux:subheading>Enter the details for the new client company.</flux:subheading>
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
            <flux:heading size="lg">Edit Company</flux:heading>
            <flux:subheading>Update the details for this company.</flux:subheading>
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
</div>
