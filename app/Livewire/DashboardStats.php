<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use Livewire\Component;

class DashboardStats extends Component
{
    public int $deviceCount = 0;

    public int $staffCount = 0;

    public int $companyCount = 0;

    public int $expiringSoon = 0;

    public function mount(): void
    {
        $this->deviceCount = Device::count();
        $this->staffCount = Staff::count();
        $this->companyCount = Company::count();
        $this->expiringSoon = Device::where('warranty_expiry', '<=', now()->addDays(30))
            ->where('warranty_expiry', '>=', now())
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
