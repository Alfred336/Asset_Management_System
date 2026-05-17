<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Device;
use App\Models\DeviceStatusHistory;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class DeviceStatusManager extends Component
{
    use WithPagination;

    public $search = '';

    public $companyFilter = '';

    public $showUpdateModal = false;

    public $showHistoryModal = false;

    public $deviceId;

    public $status;

    public $notes;

    protected $rules = [
        'status' => 'required|in:active,offline,online,formatted,dead,under_repair,retired',
        'notes' => 'nullable|string',
    ];

    public function render()
    {
        $devices = Device::with(['company', 'staff', 'statusHistory' => function ($query) {
            $query->latest()->limit(1);
        }])
            ->where(function ($query) {
                $query->where('asset_tag', 'like', '%'.$this->search.'%')
                    ->orWhere('model', 'like', '%'.$this->search.'%')
                    ->orWhere('serial_number', 'like', '%'.$this->search.'%');
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $companies = Company::where('status', 'active')->get();

        return view('livewire.device-status-manager', [
            'devices' => $devices,
            'companies' => $companies,
        ]);
    }

    public function openStatusUpdate($deviceId)
    {
        $device = Device::findOrFail($deviceId);

        if (Gate::denies('update-device-status')) {
            session()->flash('error', 'You do not have permission to update device status.');

            return;
        }

        $this->deviceId = $deviceId;
        $this->status = $device->status;
        $this->notes = '';
        $this->showUpdateModal = true;
    }

    public function updateStatus()
    {
        $this->validate();

        $device = Device::findOrFail($this->deviceId);
        $oldStatus = $device->status;

        $device->update(['status' => $this->status]);

        DeviceStatusHistory::create([
            'device_id' => $device->id,
            'status' => $this->status,
            'notes' => $this->notes,
            'changed_by' => auth()->id(),
        ]);

        $this->showUpdateModal = false;
        session()->flash('message', "Device {$device->asset_tag} status updated from {$oldStatus} to {$this->status}.");
    }

    public function viewHistory($deviceId)
    {
        $device = Device::with(['statusHistory.changedBy'])->findOrFail($deviceId);

        if (Gate::denies('view-device-status-history')) {
            session()->flash('error', 'You do not have permission to view status history.');

            return;
        }

        $this->deviceId = $deviceId;
        $this->showHistoryModal = true;
    }

    public function getDeviceHistoryProperty()
    {
        if (! $this->deviceId) {
            return collect();
        }

        return DeviceStatusHistory::where('device_id', $this->deviceId)
            ->with('changedBy')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    public function resetInputFields()
    {
        $this->deviceId = null;
        $this->status = '';
        $this->notes = '';
    }
}
