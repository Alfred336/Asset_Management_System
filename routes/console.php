<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:check-device-warranty-expiry')->daily();
Schedule::command('app:send-daily-status-update-reminders')->dailyAt('08:00');
Schedule::command('app:send-weekly-device-report')->fridays()->at('16:00');
