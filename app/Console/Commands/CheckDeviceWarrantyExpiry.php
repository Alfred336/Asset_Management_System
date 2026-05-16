<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Notifications\DeviceWarrantyExpiryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckDeviceWarrantyExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-device-warranty-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for devices with warranties expiring in the next 30 days and notify admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiryThreshold = now()->addDays(30);

        $devices = Device::where('warranty_expiry', '<=', $expiryThreshold)
            ->where('warranty_expiry', '>=', now())
            ->where('status', '!=', 'retired')
            ->get();

        if ($devices->isEmpty()) {
            $this->info('No devices with warranties expiring soon.');

            return;
        }

        $admins = User::role(['Super Admin', 'Admin'])->get();

        foreach ($devices as $device) {
            Notification::send($admins, new DeviceWarrantyExpiryNotification($device));
            $this->info('Notification sent for device: '.$device->asset_tag);
        }

        $this->info('Total notifications sent: '.$devices->count());
    }
}
