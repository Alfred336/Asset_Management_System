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

        // Optimized query to get aggregates by company
        $companyStats = Device::query()
            ->join('companies', 'devices.company_id', '=', 'companies.id')
            ->select('companies.name as company_name')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when devices.status in ("active", "online") then 1 end) as active_count')
            ->selectRaw('count(case when devices.status = "offline" then 1 end) as offline_count')
            ->selectRaw('count(case when devices.status = "dead" then 1 end) as dead_count')
            ->selectRaw('count(case when devices.status = "retired" then 1 end) as retired_count')
            ->whereNull('devices.deleted_at')
            ->groupBy('companies.id', 'companies.name')
            ->orderBy('companies.name')
            ->get();

        $grandTotals = [
            'total' => (int) $companyStats->sum('total'),
            'active' => (int) $companyStats->sum('active_count'),
            'offline' => (int) $companyStats->sum('offline_count'),
            'dead' => (int) $companyStats->sum('dead_count'),
            'retired' => (int) $companyStats->sum('retired_count'),
        ];

        $period = [
            'start' => now()->subWeek()->startOfDay()->format('M j, Y'),
            'end' => now()->format('M j, Y'),
        ];

        $pdf = $reportPdf->generate($companyStats, $grandTotals, $period);

        // Send notification to all users
        Notification::send($users, new WeeklyDeviceReportNotification(
            pdf: $pdf,
            totalDevices: $grandTotals['total'],
            offlineDevices: $grandTotals['offline'],
            formattedDevices: (int) Device::where('status', 'formatted')->count(), // For the notification body if needed
        ));

        $this->info("Weekly device status report sent to {$users->count()} users.");

        return self::SUCCESS;
    }
}
