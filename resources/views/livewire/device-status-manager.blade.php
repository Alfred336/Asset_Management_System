<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Device Status Manager</flux:heading>
            <flux:subheading>Update device status and track maintenance history.</flux:subheading>
        </div>
        <flux:badge color="amber" size="lg">{{ $devices->total() }} Devices</flux:badge>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Search devices by tag, model, or serial..." />
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
    <flux:table :paginate="$devices">
        <flux:table.columns>
            <flux:table.column>Asset Tag</flux:table.column>
            <flux:table.column>Device</flux:table.column>
            <flux:table.column>Company</flux:table.column>
            <flux:table.column>Current Status</flux:table.column>
            <flux:table.column>Last Update</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($devices as $device)
                <flux:table.row :key="$device->id">
                    <flux:table.cell class="font-medium">{{ $device->asset_tag }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span>{{ $device->model }}</span>
                            <span class="text-xs text-zinc-500">{{ $device->device_type }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $device->company->name ?? 'N/A' }}</flux:table.cell>
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
                        @if($device->statusHistory->first())
                            <flux:text size="sm">{{ $device->statusHistory->first()->created_at->diffForHumans() }}</flux:text>
                        @else
                            <flux:text size="sm" color="zinc">Never</flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button.group>
                            @can('update-device-status')
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="openStatusUpdate({{ $device->id }})" />
                            @endcan
                            @can('view-device-status-history')
                                <flux:button variant="ghost" size="sm" icon="clock" wire:click="viewHistory({{ $device->id }})" />
                            @endcan
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Update Status Modal -->
    <flux:modal wire:model="showUpdateModal" class="space-y-6">
        <div>
            <flux:heading size="lg">Update Device Status</flux:heading>
            <flux:subheading>Select the new status for this device.</flux:subheading>
        </div>

        <form wire:submit.prevent="updateStatus" class="space-y-4">
            <flux:select wire:model="status" label="Status" required>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="offline">Offline</flux:select.option>
                <flux:select.option value="online">Online</flux:select.option>
                <flux:select.option value="formatted">Formatted</flux:select.option>
                <flux:select.option value="dead">Dead</flux:select.option>
                <flux:select.option value="under_repair">Under Repair</flux:select.option>
                <flux:select.option value="retired">Retired</flux:select.option>
            </flux:select>

            <flux:textarea wire:model="notes" label="Notes (optional)" rows="3" placeholder="Add any notes about this status change..." />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="showUpdateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Status</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Status History Modal -->
    @if($showHistoryModal)
    <flux:modal wire:model="showHistoryModal" variant="wide" class="space-y-6">
        <div>
            <flux:heading size="lg">Status History</flux:heading>
            <flux:subheading>View all status changes for this device.</flux:subheading>
        </div>

        @php
            $historyItems = $deviceHistory ?? collect();
        @endphp

        @if($historyItems->isEmpty())
            <flux:text color="zinc">No status history recorded yet.</flux:text>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Changed By</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Notes</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($historyItems as $history)
                        <flux:table.row :key="$history->id">
                            <flux:table.cell>
                                <flux:badge>{{ ucfirst(str_replace('_', ' ', $history->status)) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $history->changedBy->name ?? 'System' }}</flux:table.cell>
                            <flux:table.cell>{{ $history->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                            <flux:table.cell>{{ $history->notes ?? '-' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif

        <div class="flex justify-end">
            <flux:button variant="ghost" wire:click="showHistoryModal = false">Close</flux:button>
        </div>
    </flux:modal>
    @endif
</div>
