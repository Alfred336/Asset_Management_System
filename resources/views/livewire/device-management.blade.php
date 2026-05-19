<div class="space-y-8 p-4 lg:p-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <flux:heading size="xl" level="1" class="font-black tracking-tight">Infrastructure Inventory</flux:heading>
            <flux:subheading>Global oversight of enterprise IT assets.</flux:subheading>
        </div>

        <div class="flex items-center gap-3">
            <flux:button icon="document-arrow-down" variant="ghost" wire:click="export" flux:tooltip="Export to Excel">Export</flux:button>
            @can('create-devices')
                <flux:button icon="document-arrow-up" variant="ghost" wire:click="$set('showImportModal', true)" flux:tooltip="Import from Excel">Import</flux:button>
                <flux:button icon="plus" variant="primary" wire:click="creating" class="shadow-lg shadow-pink-500/25 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700">Add Asset</flux:button>
            @endcan
        </div>
    </div>

    @if (session()->has('message'))
        <flux:badge color="success" class="w-full justify-center py-3 rounded-xl border-emerald-100 dark:border-emerald-900/30">{{ session('message') }}</flux:badge>
    @endif

    @if (session()->has('error'))
        <flux:badge color="danger" class="w-full justify-center py-2">{{ session('error') }}</flux:badge>
    @endif

    <!-- Filters -->
    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search devices by tag, serial, model..." class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-pink-500" />
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <flux:select wire:model.live="companyFilter" placeholder="All Companies" class="min-w-[160px]">
                    <flux:select.option value="">All Companies</flux:select.option>
                    @foreach($companies as $company)
                        <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="statusFilter" placeholder="All Statuses" class="min-w-[140px]">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    @foreach(['active', 'offline', 'online', 'formatted', 'dead', 'under_repair', 'retired'] as $status)
                        <flux:select.option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:checkbox wire:model.live="showTrashed" label="Archive" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden premium-card">
        <flux:table :paginate="$devices">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'asset_tag'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('asset_tag')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Identity</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortAsc ? 'asc' : 'desc'" wire:click="sortBy('model')" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Model / Type</flux:table.column>
                <flux:table.column class="hidden md:table-cell !bg-zinc-50/50 dark:!bg-zinc-800/20">Company</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Assigned Staff</flux:table.column>
                <flux:table.column class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Status</flux:table.column>
                <flux:table.column align="end" class="!bg-zinc-50/50 dark:!bg-zinc-800/20">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($devices as $device)
                    <flux:table.row :key="$device->id" class="group hover:bg-pink-50/30 dark:hover:bg-pink-900/10 transition-colors">
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">{{ $device->asset_tag }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">{{ $device->serial_number ?: 'NO SERIAL' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $device->model }}</span>
                                <span class="text-xs text-zinc-500">{{ $device->device_type }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden md:table-cell">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $device->company->name ?? 'N/A' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $device->staff?->full_name ?? 'Unassigned' }}</span>
                        </flux:table.cell>
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
                            @if($device->trashed())
                                <flux:badge color="danger" variant="solid" size="sm" class="font-bold uppercase text-[10px]">Archived</flux:badge>
                            @else
                                <flux:badge :color="$statusColor" variant="outline" size="sm" class="font-bold uppercase text-[10px]">
                                    {{ str_replace('_', ' ', $device->status) }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item icon="eye" wire:click="viewDetails({{ $device->id }})">Details</flux:menu.item>
                                    @if(!$device->trashed())
                                        @can('edit-devices')
                                            <flux:menu.item icon="pencil-square" wire:click="edit({{ $device->id }})">Edit</flux:menu.item>
                                        @endcan
                                        <flux:menu.separator />
                                        @can('delete-devices')
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $device->id }})">Delete</flux:menu.item>
                                        @endcan
                                    @else
                                        @can('restore-devices')
                                            <flux:menu.item icon="arrow-path" wire:click="restore({{ $device->id }})">Restore</flux:menu.item>
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
            <flux:heading size="lg">{{ $viewingDevice['asset_tag'] ?? 'Device Details' }}</flux:heading>
            <flux:subheading>{{ $viewingDevice['model'] ?? 'View device information.' }}</flux:subheading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @foreach([
                'Asset Tag' => $viewingDevice['asset_tag'] ?? null,
                'Serial Number' => $viewingDevice['serial_number'] ?? null,
                'Model' => $viewingDevice['model'] ?? null,
                'Manufacturer' => $viewingDevice['manufacturer'] ?? null,
                'Device Type' => $viewingDevice['device_type'] ?? null,
                'Operating System' => $viewingDevice['operating_system'] ?? null,
                'Processor' => $viewingDevice['processor'] ?? null,
                'RAM' => $viewingDevice['ram'] ?? null,
                'Storage' => $viewingDevice['storage'] ?? null,
                'IP Address' => $viewingDevice['ip_address'] ?? null,
                'MAC Address' => $viewingDevice['mac_address'] ?? null,
                'Hostname' => $viewingDevice['hostname'] ?? null,
                'Location' => $viewingDevice['location'] ?? null,
                'Company' => $viewingDevice['company'] ?? null,
                'Assigned Staff' => $viewingDevice['staff'] ?? null,
                'Status' => $viewingDevice['status'] ?? null,
                'Purchase Date' => $viewingDevice['purchase_date'] ?? null,
                'Purchase Cost' => $viewingDevice['purchase_cost'] ?? null,
                'Warranty Expiry' => $viewingDevice['warranty_expiry'] ?? null,
                'Deleted At' => $viewingDevice['deleted_at'] ?? null,
            ] as $label => $value)
                <div>
                    <div class="text-xs font-medium text-zinc-500">{{ $label }}</div>
                    <div class="mt-1 text-zinc-900 dark:text-zinc-100">{{ filled($value) ? $value : 'N/A' }}</div>
                </div>
            @endforeach

            <div class="md:col-span-2">
                <div class="text-xs font-medium text-zinc-500">Notes</div>
                <div class="mt-1 whitespace-pre-line text-zinc-900 dark:text-zinc-100">{{ filled($viewingDevice['notes'] ?? null) ? $viewingDevice['notes'] : 'N/A' }}</div>
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
            <flux:heading size="lg">Add New Device</flux:heading>
            <flux:subheading>Enter the details for the new IT asset.</flux:subheading>
        </div>

          <form wire:submit.prevent="create" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <flux:select wire:model="company_id" label="Company" required placeholder="Select a company" wire:change="loadStaffForCompany">
                  <flux:select.option value="">Select a company</flux:select.option>
                  @foreach($companies as $company)
                      <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                  @endforeach
              </flux:select>

             <flux:select wire:model="staff_id" label="Assigned Staff (Optional)" placeholder="Unassigned">
                 <flux:select.option value="">Unassigned</flux:select.option>
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
                <flux:radio.group wire:model="storage_type" label="Storage type">
                    <flux:radio value="HDD" label="HDD" checked />
                    <flux:radio value="SSD" label="SSD" />
                    <flux:radio value="NVMe" label="NVMe" />
                    <flux:radio value="eMMC" label="eMMC" />
                    <flux:radio value="Hybrid" label="Hybrid" />
                </flux:radio.group>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="ip_address" label="IP Address" />
                <flux:input wire:model="mac_address" label="MAC Address" placeholder="00:00:00:00:00:00" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="hostname" label="Hostname" />
                <flux:input wire:model="location" label="Location" />
            </div>

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
              <flux:select wire:model="company_id" label="Company" required placeholder="Select a company" wire:change="loadStaffForCompany">
                  <flux:select.option value="">Select a company</flux:select.option>
                  @foreach($companies as $company)
                      <flux:select.option value="{{ $company->id }}">{{ $company->name }}</flux:select.option>
                  @endforeach
              </flux:select>

             <flux:select wire:model="staff_id" label="Assigned Staff (Optional)" placeholder="Unassigned">
                 <flux:select.option value="">Unassigned</flux:select.option>
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

    <!-- Import Modal -->
    <flux:modal wire:model="showImportModal" class="space-y-6">
        <div>
            <flux:heading size="lg">Import Devices</flux:heading>
            <flux:subheading>Upload an Excel file (.xlsx, .xls or .csv) to import devices.</flux:subheading>
        </div>

        <form wire:submit.prevent="import" class="space-y-6">
            <flux:input type="file" wire:model="importFile" label="Excel/CSV File" required />
            
            @if($importErrors)
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 space-y-1">
                    <p class="font-bold">Import failed with following errors:</p>
                    <ul class="list-disc list-inside overflow-auto max-h-40">
                        @foreach($importErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showImportModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="import">Import</span>
                    <span wire:loading wire:target="import">Importing...</span>
                </flux:button>
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
            <flux:subheading>Are you sure you want to delete this asset? This action will move it to the archive.</flux:subheading>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" class="flex-1" wire:click="showDeleteConfirmation = false">Cancel</flux:button>
            <flux:button variant="danger" class="flex-1" wire:click="confirmDelete">Delete Asset</flux:button>
        </div>
    </flux:modal>
</div>
