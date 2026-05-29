<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Notifications\WeeklyDeviceReportNotification;
use App\Services\DeviceReportPdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendWeeklyDeviceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-device-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the weekly device summary report PDF to all active users.';

    /**
     * Execute the console command.
     */
    public function handle(DeviceReportPdf $reportPdf): int
    {
        $users = User::query()
            ->whereNotNull('email')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No active users found to receive the report.');
            return self::SUCCESS;
        }

        $reportData = $reportPdf->getReportData();
        $pdf = $reportPdf->generate(
            $reportData['companyStats'],
            $reportData['grandTotals'],
            $reportData['period']
        );

        $grandTotals = $reportData['grandTotals'];

        // Send notification to all users
        Notification::send($users, new WeeklyDeviceReportNotification(
            pdf: $pdf,
            totalDevices: $grandTotals['total'],
            offlineDevices: $grandTotals['offline'],
            formattedDevices: (int) Device::where('status', 'formatted')->count(),
        ));

        $this->info("Weekly device status report sent to {$users->count()} users.");

        return self::SUCCESS;
    }
}
