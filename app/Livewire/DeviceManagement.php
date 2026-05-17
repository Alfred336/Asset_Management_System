<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class DeviceManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortAsc = false;

    public $companyFilter = '';

    public $statusFilter = '';

    public $deviceTypeFilter = '';

    public $showTrashed = false;

    // For create/edit modal
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDetailsModal = false;

    public array $viewingDevice = [];

    public $deviceId;

    public $company_id;

    public $staff_id;

    public $asset_tag;

    public $serial_number;

    public $model;

    public $manufacturer;

    public $device_type;

    public $operating_system;

    public $os_version;

    public $processor;

    public $ram_gb;

    public $storage_gb;

    public $storage_type;

    public $ip_address;

    public $mac_address;

    public $hostname;

    public $location;

    public $status;

    public $purchase_date;

    public $purchase_cost;

    public $warranty_expiry;

    public $notes;

    protected $rules = [
        'company_id' => 'required|exists:companies,id',
        'staff_id' => 'nullable|exists:staff,id',
        'asset_tag' => 'required|string|max:100|unique:devices,asset_tag',
        'serial_number' => 'nullable|string|max:100|unique:devices,serial_number',
        'model' => 'required|string|max:100',
        'manufacturer' => 'nullable|string|max:100',
        'device_type' => 'required|string|max:100',
        'operating_system' => 'nullable|string|max:100',
        'os_version' => 'nullable|string|max:50',
        'processor' => 'nullable|string|max:100',
        'ram_gb' => 'nullable|integer|min:1',
        'storage_gb' => 'nullable|integer|min:1',
        'storage_type' => 'nullable|string|max:50',
        'ip_address' => 'nullable|ip',
        'mac_address' => 'nullable|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
        'hostname' => 'nullable|string|max:100',
        'location' => 'nullable|string|max:100',
        'status' => 'required|in:active,offline,online,formatted,dead,under_repair,retired',
        'purchase_date' => 'nullable|date',
        'purchase_cost' => 'nullable|numeric|between:0,999999.99',
        'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'asset_tag.unique' => 'The asset tag has already been taken.',
        'serial_number.unique' => 'The serial number has already been taken.',
        'mac_address.regex' => 'The MAC address format is invalid.',
        'ip_address.ip' => 'The IP address is invalid.',
        'warranty_expiry.after_or_equal' => 'The warranty expiry date must be on or after the purchase date.',
    ];

    public function mount()
    {
        $this->status = 'active';
    }

    public function render()
    {
        $companies = Company::where('status', 'active')->get();
        $staff = Staff::where('status', 'active')->get();

        $deviceQuery = Device::with(['company', 'staff'])
            ->when($this->showTrashed, fn ($query) => $query->withTrashed())
            ->where(function ($query) {
                $query->where('asset_tag', 'like', '%'.$this->search.'%')
                    ->orWhere('serial_number', 'like', '%'.$this->search.'%')
                    ->orWhere('model', 'like', '%'.$this->search.'%')
                    ->orWhere('hostname', 'like', '%'.$this->search.'%');
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->deviceTypeFilter, function ($query) {
                $query->where('device_type', $this->deviceTypeFilter);
            })
            ->when(! $this->showTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');

        $devices = $deviceQuery->paginate($this->perPage);

        // Get unique device types for filter dropdown
        $deviceTypes = Device::whereNull('deleted_at')
            ->orderBy('device_type')
            ->pluck('device_type')
            ->unique();

        return view('livewire.device-management', [
            'devices' => $devices,
            'companies' => $companies,
            'staff' => $staff,
            'deviceTypes' => $deviceTypes,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function creating()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->validate();

        // Check if the user has permission to create devices
        if (Gate::denies('create-devices')) {
            session()->flash('error', 'You do not have permission to create devices.');

            return;
        }

        Device::create([
            'company_id' => $this->company_id,
            'staff_id' => $this->staff_id,
            'asset_tag' => $this->asset_tag,
            'serial_number' => $this->serial_number,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'device_type' => $this->device_type,
            'operating_system' => $this->operating_system,
            'os_version' => $this->os_version,
            'processor' => $this->processor,
            'ram_gb' => $this->ram_gb,
            'storage_gb' => $this->storage_gb,
            'storage_type' => $this->storage_type,
            'ip_address' => $this->ip_address,
            'mac_address' => $this->mac_address,
            'hostname' => $this->hostname,
            'location' => $this->location,
            'status' => $this->status,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'warranty_expiry' => $this->warranty_expiry,
            'notes' => $this->notes,
        ]);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Device created successfully.');
    }

    public function edit($id)
    {
        $device = Device::withTrashed()->with(['company', 'staff'])->findOrFail($id);

        // Check if the user has permission to edit devices
        if (Gate::denies('edit-devices')) {
            session()->flash('error', 'You do not have permission to edit devices.');

            return;
        }

        $this->deviceId = $id;
        $this->company_id = $device->company_id;
        $this->staff_id = $device->staff_id;
        $this->asset_tag = $device->asset_tag;
        $this->serial_number = $device->serial_number;
        $this->model = $device->model;
        $this->manufacturer = $device->manufacturer;
        $this->device_type = $device->device_type;
        $this->operating_system = $device->operating_system;
        $this->os_version = $device->os_version;
        $this->processor = $device->processor;
        $this->ram_gb = $device->ram_gb;
        $this->storage_gb = $device->storage_gb;
        $this->storage_type = $device->storage_type;
        $this->ip_address = $device->ip_address;
        $this->mac_address = $device->mac_address;
        $this->hostname = $device->hostname;
        $this->location = $device->location;
        $this->status = $device->status;
        $this->purchase_date = $device->purchase_date;
        $this->purchase_cost = $device->purchase_cost;
        $this->warranty_expiry = $device->warranty_expiry;
        $this->notes = $device->notes;

        $this->showEditModal = true;
    }

    public function viewDetails($id)
    {
        if (Gate::denies('view-devices')) {
            session()->flash('error', 'You do not have permission to view devices.');

            return;
        }

        $device = Device::withTrashed()->with(['company', 'staff'])->findOrFail($id);

        $this->viewingDevice = [
            'asset_tag' => $device->asset_tag,
            'serial_number' => $device->serial_number,
            'model' => $device->model,
            'manufacturer' => $device->manufacturer,
            'device_type' => $device->device_type,
            'operating_system' => trim(collect([$device->operating_system, $device->os_version])->filter()->join(' ')),
            'processor' => $device->processor,
            'ram' => $device->ram_gb ? $device->ram_gb.' GB' : null,
            'storage' => $device->storage_gb ? trim($device->storage_gb.' GB '.$device->storage_type) : $device->storage_type,
            'ip_address' => $device->ip_address,
            'mac_address' => $device->mac_address,
            'hostname' => $device->hostname,
            'location' => $device->location,
            'company' => $device->company?->name,
            'staff' => $device->staff?->full_name,
            'status' => ucfirst(str_replace('_', ' ', $device->status)),
            'purchase_date' => $device->purchase_date?->format('Y-m-d'),
            'purchase_cost' => $device->purchase_cost ? number_format((float) $device->purchase_cost, 2) : null,
            'warranty_expiry' => $device->warranty_expiry?->format('Y-m-d'),
            'notes' => $device->notes,
            'deleted_at' => $device->deleted_at?->format('Y-m-d H:i'),
        ];

        $this->showDetailsModal = true;
    }

    public function update()
    {
        $this->validate([
            'asset_tag' => 'required|string|max:100|unique:devices,asset_tag,'.$this->deviceId,
            'serial_number' => 'nullable|string|max:100|unique:devices,serial_number,'.$this->deviceId,
            'mac_address' => 'nullable|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'ip_address' => 'nullable|ip',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
        ]);

        // Check if the user has permission to edit devices
        if (Gate::denies('edit-devices')) {
            session()->flash('error', 'You do not have permission to edit devices.');

            return;
        }

        $device = Device::withTrashed()->findOrFail($this->deviceId);
        $device->update([
            'company_id' => $this->company_id,
            'staff_id' => $this->staff_id,
            'asset_tag' => $this->asset_tag,
            'serial_number' => $this->serial_number,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'device_type' => $this->device_type,
            'operating_system' => $this->operating_system,
            'os_version' => $this->os_version,
            'processor' => $this->processor,
            'ram_gb' => $this->ram_gb,
            'storage_gb' => $this->storage_gb,
            'storage_type' => $this->storage_type,
            'ip_address' => $this->ip_address,
            'mac_address' => $this->mac_address,
            'hostname' => $this->hostname,
            'location' => $this->location,
            'status' => $this->status,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'warranty_expiry' => $this->warranty_expiry,
            'notes' => $this->notes,
        ]);

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Device updated successfully.');
    }

    public function delete($id)
    {
        // Check if the user has permission to delete devices
        if (Gate::denies('delete-devices')) {
            session()->flash('error', 'You do not have permission to delete devices.');

            return;
        }

        $device = Device::findOrFail($id);
        $device->delete();

        session()->flash('message', 'Device deleted successfully.');
    }

    public function restore($id)
    {
        if (Gate::denies('restore-devices')) {
            session()->flash('error', 'You do not have permission to restore devices.');

            return;
        }

        $device = Device::withTrashed()->findOrFail($id);
        $device->restore();

        session()->flash('message', 'Device restored successfully.');
    }

    public function resetInputFields()
    {
        $this->company_id = '';
        $this->staff_id = '';
        $this->asset_tag = '';
        $this->serial_number = '';
        $this->model = '';
        $this->manufacturer = '';
        $this->device_type = '';
        $this->operating_system = '';
        $this->os_version = '';
        $this->processor = '';
        $this->ram_gb = '';
        $this->storage_gb = '';
        $this->storage_type = '';
        $this->ip_address = '';
        $this->mac_address = '';
        $this->hostname = '';
        $this->location = '';
        $this->status = 'active';
        $this->purchase_date = '';
        $this->purchase_cost = '';
        $this->warranty_expiry = '';
        $this->notes = '';
    }
}
