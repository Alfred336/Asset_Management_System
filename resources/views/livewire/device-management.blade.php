<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search, Filters and Create Button -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 md:flex-row md:justify-between">
            <div class="w-full md:w-auto">
                <input type="text"
                       wire:model.debounce.500ms="search"
                       placeholder="Search devices..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mt-4 md:mt-0 md:w-auto">
                <select wire:model="companyFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $companyFilter == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4 md:mt-0 md:w-auto">
                <select wire:model="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach(['active', 'offline', 'online', 'formatted', 'dead', 'under_repair', 'retired'] as $status)
                        <option value="{{ $status }}" {{ $statusFilter == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4 md:mt-0 md:w-auto">
                <select wire:model="deviceTypeFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($deviceTypes as $type)
                        <option value="{{ $type }}" {{ $deviceTypeFilter == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <button wire:click="creating"
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    @can('create-devices')
                    >
                Add Device
            </button>
        </div>
    </div>

    <!-- Sortable Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('asset_tag')"
                                class="flex items-center">
                            Asset Tag
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'asset_tag') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('model')"
                                class="flex items-center">
                            Model
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'model') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Company</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Assigned To</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Type</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($devices->isEmpty())
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center text-gray-500">
                            No devices found.
                        </td>
                    </tr>
                @else
                    @foreach($devices as $device)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $device->asset_tag }}</td>
                            <td class="px-4 py-2">{{ $device->model }}</td>
                            <td class="px-4 py-2">{{ $device->company->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                @if($device->staff)
                                    {{ $device->staff->full_name }}
                                @else
                                    <span class="text-gray-500">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($device->status === 'active') bg-green-100 text-green-800
                                        @elseif($device->status === 'offline') bg-gray-100 text-gray-800
                                        @elseif($device->status === 'online') bg-blue-100 text-blue-800
                                        @elseif($device->status === 'formatted') bg-yellow-100 text-yellow-800
                                        @elseif($device->status === 'dead') bg-red-100 text-red-800
                                        @elseif($device->status === 'under_repair') bg-orange-100 text-orange-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $device->device_type ?? 'N/A' }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $device->id }})"
                                        class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                                        @can('edit-devices')
                                        >
                                    Edit
                                </button>
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $device->id }})"
                                        class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        @can('delete-devices')
                                        >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <span class="text-sm text-gray-500">
            Showing {{$devices->firstItem()}} to {{$devices->lastItem()}} of {{$devices->total()}} entries
        </span>
        <div>
            {{ $devices->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div wire:ignore.self class="fixed z-50 inset-0 overflow-y-auto hidden @if($showCreateModal) flex @endif" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-2xl max-h-full">
            <!-- Content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                        Add Device
                    </h3>
                    <button type="button"
                            class="flex h-8 w-8 text-gray-400 bg-transparent rounded-md hover:bg-gray-200 hover:text-gray-900"
                            wire:click="showCreateModal = false"
                            >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-6 space-y-6">
                    <form wire:submit.prevent="create">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <select id="company_id"
                                        wire:model="company_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required>
                                    <option value="">Select a company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff (Optional)</label>
                                <select id="staff_id"
                                        wire:model="staff_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">{{ $staffMember->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('staff_id') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label for="asset_tag" class="block text-sm font-medium text-gray-700 mb-1">Asset Tag</label>
                                <input type="text"
                                       id="asset_tag"
                                       wire:model="asset_tag"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('asset_tag') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number (Optional)</label>
                                <input type="text"
                                       id="serial_number"
                                       wire:model="serial_number"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('serial_number') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                <input type="text"
                                       id="model"
                                       wire:model="model"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('model') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Manufacturer (Optional)</label>
                                <input type="text"
                                       id="manufacturer"
                                       wire:model="manufacturer"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('manufacturer') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1">Device Type</label>
                                <input type="text"
                                       id="device_type"
                                       wire:model="device_type"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('device_type') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="operating_system" class="block text-sm font-medium text-gray-700 mb-1">Operating System (Optional)</label>
                                <input type="text"
                                       id="operating_system"
                                       wire:model="operating_system"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('operating_system') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="os_version" class="block text-sm font-medium text-gray-700 mb-1>OS Version</label>
                                    <input type="text"
                                           id="os_version"
                                           wire:model="os_version"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="processor" class="block text-sm font-medium text-gray-700 mb-1>Processor</label>
                                    <input type="text"
                                           id="processor"
                                           wire:model="processor"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ram_gb" class="block text-sm font-medium text-gray-700 mb-1>RAM (GB)</label>
                                    <input type="number"
                                           id="ram_gb"
                                           wire:model="ram_gb"
                                           min="1"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="storage_gb" class="block text-sm font-medium text-gray-700 mb-1>Storage (GB)</label>
                                    <input type="number"
                                           id="storage_gb"
                                           wire:model="storage_gb"
                                           min="1"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="storage_type" class="block text-sm font-medium text-gray-700 mb-1>Storage Type (Optional)</label>
                                <input type="text"
                                       id="storage_type"
                                       wire:model="storage_type"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('storage_type') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1>IP Address</label>
                                    <input type="text"
                                           id="ip_address"
                                           wire:model="ip_address"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="mac_address" class="block text-sm font-medium text-gray-700 mb-1>MAC Address</label>
                                    <input type="text"
                                           id="mac_address"
                                           wire:model="mac_address"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hostname" class="block text-sm font-medium text-gray-700 mb-1>Hostname</label>
                                    <input type="text"
                                           id="hostname"
                                           wire:model="hostname"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1>Location</label>
                                    <input type="text"
                                           id="location"
                                           wire:model="location"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1>Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="active">Active</option>
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                        <option value="formatted">Formatted</option>
                                        <option value="dead">Dead</option>
                                        <option value="under_repair">Under Repair</option>
                                        <option value="retired">Retired</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1>Purchase Date</label>
                                    <input type="date"
                                           id="purchase_date"
                                           wire:model="purchase_date"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="purchase_cost" class="block text-sm font-medium text-gray-700 mb-1>Purchase Cost</label>
                                    <input type="number"
                                           id="purchase_cost"
                                           wire:model="purchase_cost"
                                           step="0.01"
                                           min="0"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-1>Warranty Expiry</label>
                                    <input type="date"
                                           id="warranty_expiry"
                                           wire:model="warranty_expiry"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1>Notes</label>
                                <textarea id="notes"
                                          wire:model="notes"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('notes') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Add Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
             wire:click="showCreateModal = false"
        ></div>
    </div>

    <!-- Edit Modal -->
    <div wire:ignore.self class="fixed z-50 inset-0 overflow-y-auto hidden @if($showEditModal) flex @endif" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-2xl max-h-full">
            <!-- Content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                        Edit Device
                    </h3>
                    <button type="button"
                            class="flex h-8 w-8 text-gray-400 bg-transparent rounded-md hover:bg-gray-200 hover:text-gray-900"
                            wire:click="showEditModal = false"
                            >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-6 space-y-6">
                    <form wire:submit.prevent="update">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1>Company</label>
                                <select id="company_id"
                                        wire:model="company_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required>
                                    <option value="">Select a company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1>Assigned Staff (Optional)</label>
                                <select id="staff_id"
                                        wire:model="staff_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">{{ $staffMember->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('staff_id') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label for="asset_tag" class="block text-sm font-medium text-gray-700 mb-1>Asset Tag</label>
                                <input type="text"
                                       id="asset_tag"
                                       wire:model="asset_tag"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('asset_tag') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1>Serial Number (Optional)</label>
                                <input type="text"
                                       id="serial_number"
                                       wire:model="serial_number"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('serial_number') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-1>Model</label>
                                <input type="text"
                                       id="model"
                                       wire:model="model"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('model') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1>Manufacturer (Optional)</label>
                                <input type="text"
                                       id="manufacturer"
                                       wire:model="manufacturer"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('manufacturer') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1>Device Type</label>
                                <input type="text"
                                       id="device_type"
                                       wire:model="device_type"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('device_type') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="operating_system" class="block text-sm font-medium text-gray-700 mb-1>Operating System (Optional)</label>
                                <input type="text"
                                       id="operating_system"
                                       wire:model="operating_system"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('operating_system') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="os_version" class="block text-sm font-medium text-gray-700 mb-1>OS Version</label>
                                    <input type="text"
                                           id="os_version"
                                           wire:model="os_version"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="processor" class="block text-sm font-medium text-gray-700 mb-1>Processor</label>
                                    <input type="text"
                                           id="processor"
                                           wire:model="processor"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ram_gb" class="block text-sm font-medium text-gray-700 mb-1>RAM (GB)</label>
                                    <input type="number"
                                           id="ram_gb"
                                           wire:model="ram_gb"
                                           min="1"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="storage_gb" class="block text-sm font-medium text-gray-700 mb-1>Storage (GB)</label>
                                    <input type="number"
                                           id="storage_gb"
                                           wire:model="storage_gb"
                                           min="1"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="storage_type" class="block text-sm font-medium text-gray-700 mb-1>Storage Type (Optional)</label>
                                <input type="text"
                                       id="storage_type"
                                       wire:model="storage_type"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('storage_type') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1>IP Address</label>
                                    <input type="text"
                                           id="ip_address"
                                           wire:model="ip_address"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="mac_address" class="block text-sm font-medium text-gray-700 mb-1>MAC Address</label>
                                    <input type="text"
                                           id="mac_address"
                                           wire:model="mac_address"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hostname" class="block text-sm font-medium text-gray-700 mb-1>Hostname</label>
                                    <input type="text"
                                           id="hostname"
                                           wire:model="hostname"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1>Location</label>
                                    <input type="text"
                                           id="location"
                                           wire:model="location"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1>Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="active">Active</option>
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                        <option value="formatted">Formatted</option>
                                        <option value="dead">Dead</option>
                                        <option value="under_repair">Under Repair</option>
                                        <option value="retired">Retired</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1>Purchase Date</label>
                                    <input type="date"
                                           id="purchase_date"
                                           wire:model="purchase_date"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="purchase_cost" class="block text-sm font-medium text-gray-700 mb-1>Purchase Cost</label>
                                    <input type="number"
                                           id="purchase_cost"
                                           wire:model="purchase_cost"
                                           step="0.01"
                                           min="0"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-1>Warranty Expiry</label>
                                    <input type="date"
                                           id="warranty_expiry"
                                           wire:model="warranty_expiry"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1>Notes</label>
                                <textarea id="notes"
                                          wire:model="notes"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('notes') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
             wire:click="showEditModal = false"
        ></div>
    </div>
</div>