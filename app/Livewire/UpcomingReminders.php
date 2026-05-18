<?php

namespace App\Livewire;

use App\Models\Device;
use Livewire\Component;

class UpcomingReminders extends Component
{
    public $devices;

    public function mount(): void
    {
        $this->devices = Device::with('company')
            ->where('warranty_expiry', '<=', now()->addDays(60))
            ->where('warranty_expiry', '>=', now())
            ->orderBy('warranty_expiry', 'asc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.upcoming-reminders');
    }
}
