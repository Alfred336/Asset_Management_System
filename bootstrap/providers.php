<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\TimezoneServiceProvider;
use App\Providers\VoltServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    TimezoneServiceProvider::class,
    VoltServiceProvider::class,
];
