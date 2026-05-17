<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Notifications\WeeklyDeviceReportNotification;
use App\Services\DeviceReportPdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWeeklyDeviceReport extends Command
{
    protected $signature = 'app:send-weekly-device-report';

    protected $description = 'Send the weekly device report PDF to all active users.';

    public function handle(DeviceReportPdf $reportPdf): int
    {
        $users = User::query()
            ->whereNotNull('email')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found to receive the weekly device report.');

            return self::SUCCESS;
        }

        $devices = Device::with(['company', 'staff'])
            ->orderBy('asset_tag')
            ->get();

        $statusCounts = Device::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $pdf = $reportPdf->generate($devices, $statusCounts);

        Notification::send($users, new WeeklyDeviceReportNotification(
            pdf: $pdf,
            totalDevices: $devices->count(),
            offlineDevices: $statusCounts['offline'] ?? 0,
            formattedDevices: $statusCounts['formatted'] ?? 0,
        ));

        $this->info("Weekly device report sent to {$users->count()} users.");

        return self::SUCCESS;
    }
}
