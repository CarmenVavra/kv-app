<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('appointments:send-reminders')->everyFifteenMinutes();
Schedule::command('invoices:generate-recurring')->daily();
Schedule::command('reports:weekly-summary')->weeklyOn(1, '07:00');
