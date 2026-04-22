<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh the local taxa table from PBDB on the 1st of each month at 02:00.
// Uses --fresh to clear stale entries before re-seeding.
Schedule::command('taxa:seed --fresh')
    ->monthlyOn(1, '02:00')
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Monthly taxa:seed failed.');
    });
