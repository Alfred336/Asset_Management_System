<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Notifications\TechnicianBulkStatusUpdateReminder;
use Illuminate\Console\Command;

class SendDailyStatusUpdateReminders extends Command
{
    protected $signature = 'app:send-daily-status-update-reminders';

    protected $description = 'Send daily reminders to technicians for devices needing status updates';

    public function handle(): void
    {
        $technicians = User::role('Technician')->get();

        if ($technicians->isEmpty()) {
            $this->info('No technicians found with the "Technician" role.');

            return;
        }

        // A device is "stale" if it hasn't been updated in 7 days
        $staleThreshold = now()->subDays(7);

        $staleDevices = Device::with('company')
            ->whereNotIn('status', ['retired', 'dead'])
            ->where('updated_at', '<', $staleThreshold)
            ->get();

        if ($staleDevices->isEmpty()) {
            $this->info('No stale devices found requiring updates.');

            return;
        }

        $sentCount = 0;
        foreach ($technicians as $technician) {
            $technician->notify(new TechnicianBulkStatusUpdateReminder($staleDevices));
            $sentCount++;
        }

        $this->info("Sent bulk status update reminders to {$sentCount} technicians for {$staleDevices->count()} stale devices.");
    }
}
