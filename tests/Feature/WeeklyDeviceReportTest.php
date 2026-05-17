<?php

use App\Models\Company;
use App\Models\Device;
use App\Models\User;
use App\Notifications\WeeklyDeviceReportNotification;
use Illuminate\Support\Facades\Notification;

test('weekly device report is sent to all active users', function () {
    Notification::fake();

    $users = User::factory()->count(2)->create();
    $company = Company::create([
        'name' => 'Report Company',
        'email' => 'report@example.com',
        'status' => 'active',
    ]);

    Device::create([
        'company_id' => $company->id,
        'asset_tag' => 'OFF-001',
        'model' => 'ThinkPad',
        'device_type' => 'Laptop',
        'status' => 'offline',
    ]);

    Device::create([
        'company_id' => $company->id,
        'asset_tag' => 'FMT-001',
        'model' => 'OptiPlex',
        'device_type' => 'Desktop',
        'status' => 'formatted',
    ]);

    $this->artisan('app:send-weekly-device-report')
        ->expectsOutput('Weekly device report sent to 2 users.')
        ->assertSuccessful();

    Notification::assertSentTo($users, WeeklyDeviceReportNotification::class);
});
