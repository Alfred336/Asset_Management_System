<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Devices</flux:heading>
            <flux:subheading>Manage your IT assets across companies.</flux:subheading>
        </div>

        @can('create-devices')
            <flux:button icon="plus" variant="primary" wire:click="creating">Add Device</flux:button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search devices by tag, serial, model..." />
        </div>
        <div class="flex gap-4">
            <flux:select wire:model.live="companyFilter" placeholder="All Companies">
                <flux:select.option value="">All Companies</flux:select.option>
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach(['active', 'offline', 'online', 'formatted', 'dead', 'under_repair', 'retired'] as $status)
                    <flux:select.option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="deviceTypeFilter" placeholder="All Types">
                <flux:select.option value="">All Types</flux:select.option>
                @foreach($deviceTypes as $type)
                    <flux:select.option value="{{ $type }}">{{ ucfirst($type) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <flux:table :paginate="$devices">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortField === 'asset_tag'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('asset_tag')">Asset Tag</flux:table.column>
            <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('model')">Model</flux:table.column>
            <flux:table.column>Company</flux:table.column>
            <flux:table.column>Assigned To</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Warranty</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($devices as $device)
                <flux:table.row :key="$device->id">
                    <flux:table.cell class="font-medium">{{ $device->asset_tag }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span>{{ $device->model }}</span>
                            <span class="text-xs text-zinc-500">{{ $device->serial_number }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $device->company->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $device->staff->full_name ?? 'Unassigned' }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            $statusColor = match($device->status) {
                                'active', 'online' => 'success',
                                'offline', 'formatted' => 'zinc',
                                'dead' => 'danger',
                                'under_repair' => 'warning',
                                'retired' => 'zinc',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge :color="$statusColor">
                            {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($device->warranty_expiry)
                            @if($device->warranty_expiry->isPast())
                                <flux:badge color="danger" size="sm">Expired</flux:badge>
                            @elseif($device->warranty_expiry->diffInDays(now()) < 30)
                                <flux:badge color="warning" size="sm">Expiring Soon</flux:badge>
                            @else
                                <flux:text size="sm">{{ $device->warranty_expiry->format('Y-m-d') }}</flux:text>
                            @endif
                        @else
                            <flux:text size="sm" color="zinc">N/A</flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button.group>
                            @can('edit-devices')
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $device->id }})" />
                            @endcan
                            @can('delete-devices')
                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $device->id }})" />
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
            <flux:heading size="lg">Add New Device</flux:heading>
            <flux:subheading>Enter the details for the new IT asset.</flux:subheading>
        </div>

        <form wire:submit.prevent="create" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="company_id" label="Company" required placeholder="Select a company">
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="staff_id" label="Assigned Staff (Optional)" placeholder="Unassigned">
                @foreach($staff as $staffMember)
                    <flux:select.option value="{{ $staffMember->id }}">{{ $staffMember->full_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="asset_tag" label="Asset Tag" required />
            <flux:input wire:model="serial_number" label="Serial Number" />
            <flux:input wire:model="model" label="Model" required />
            <flux:input wire:model="manufacturer" label="Manufacturer" />
            <flux:input wire:model="device_type" label="Device Type" required />
            <flux:input wire:model="operating_system" label="Operating System" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="os_version" label="OS Version" />
                <flux:input wire:model="processor" label="Processor" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="ram_gb" label="RAM (GB)" min="1" />
                <flux:input type="number" wire:model="storage_gb" label="Storage (GB)" min="1" />
            </div>

            <flux:input wire:model="ip_address" label="IP Address" />
            <flux:input wire:model="mac_address" label="MAC Address" placeholder="00:00:00:00:00:00" />
            <flux:input wire:model="hostname" label="Hostname" />
            <flux:input wire:model="location" label="Location" />

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="offline">Offline</flux:select.option>
                <flux:select.option value="online">Online</flux:select.option>
                <flux:select.option value="formatted">Formatted</flux:select.option>
                <flux:select.option value="dead">Dead</flux:select.option>
                <flux:select.option value="under_repair">Under Repair</flux:select.option>
                <flux:select.option value="retired">Retired</flux:select.option>
            </flux:select>

            <flux:input type="date" wire:model="purchase_date" label="Purchase Date" />
            <flux:input type="number" step="0.01" wire:model="purchase_cost" label="Purchase Cost" min="0" />
            <flux:input type="date" wire:model="warranty_expiry" label="Warranty Expiry" />

            <div class="col-span-full">
                <flux:textarea wire:model="notes" label="Notes" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showCreateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Add Device</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Device</flux:heading>
            <flux:subheading>Update the details for this IT asset.</flux:subheading>
        </div>

        <form wire:submit.prevent="update" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="company_id" label="Company" required placeholder="Select a company">
                @foreach($companies as $company)
                    <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="staff_id" label="Assigned Staff (Optional)" placeholder="Unassigned">
                @foreach($staff as $staffMember)
                    <flux:select.option value="{{ $staffMember->id }}">{{ $staffMember->full_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="asset_tag" label="Asset Tag" required />
            <flux:input wire:model="serial_number" label="Serial Number" />
            <flux:input wire:model="model" label="Model" required />
            <flux:input wire:model="manufacturer" label="Manufacturer" />
            <flux:input wire:model="device_type" label="Device Type" required />
            <flux:input wire:model="operating_system" label="Operating System" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="os_version" label="OS Version" />
                <flux:input wire:model="processor" label="Processor" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="ram_gb" label="RAM (GB)" min="1" />
                <flux:input type="number" wire:model="storage_gb" label="Storage (GB)" min="1" />
            </div>

            <flux:input wire:model="ip_address" label="IP Address" />
            <flux:input wire:model="mac_address" label="MAC Address" />
            <flux:input wire:model="hostname" label="Hostname" />
            <flux:input wire:model="location" label="Location" />

            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="offline">Offline</flux:select.option>
                <flux:select.option value="online">Online</flux:select.option>
                <flux:select.option value="formatted">Formatted</flux:select.option>
                <flux:select.option value="dead">Dead</flux:select.option>
                <flux:select.option value="under_repair">Under Repair</flux:select.option>
                <flux:select.option value="retired">Retired</flux:select.option>
            </flux:select>

            <flux:input type="date" wire:model="purchase_date" label="Purchase Date" />
            <flux:input type="number" step="0.01" wire:model="purchase_cost" label="Purchase Cost" min="0" />
            <flux:input type="date" wire:model="warranty_expiry" label="Warranty Expiry" />

            <div class="col-span-full">
                <flux:textarea wire:model="notes" label="Notes" rows="3" />
            </div>

            <div class="flex col-span-full gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showEditModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Device</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
