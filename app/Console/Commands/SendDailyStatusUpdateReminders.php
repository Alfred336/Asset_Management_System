<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Notifications\TechnicianStatusUpdateReminder;
use Illuminate\Console\Command;

class SendDailyStatusUpdateReminders extends Command
{
    protected $signature = 'app:send-daily-status-update-reminders';

    protected $description = 'Send daily reminders to technicians to update device status';

    public function handle(): void
    {
        $technicians = User::role('Technician')->get();

        if ($technicians->isEmpty()) {
            $this->info('No technicians found.');

            return;
        }

        $devices = Device::with('company')
            ->where('status', '!=', 'retired')
            ->where('status', '!=', 'dead')
            ->get();

        if ($devices->isEmpty()) {
            $this->info('No active devices found.');

            return;
        }

        $sentCount = 0;

        foreach ($technicians as $technician) {
            foreach ($devices as $device) {
                $technician->notify(new TechnicianStatusUpdateReminder($device));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} status update reminders to {$technicians->count()} technicians.");
    }
}
