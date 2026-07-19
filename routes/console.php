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

// Alle Mailables (PaymentReminderMail, AppointmentReminderMail, NewInquiryNotification,
// WeeklySummaryMail) implementieren ShouldQueue mit QUEUE_CONNECTION=database. Auf einem
// Netcup-Host ohne dauerhaften Worker-Prozess (kein Supervisor/systemd garantiert) wuerde die
// jobs-Tabelle sonst nie geleert - dieser Schedule-Eintrag nutzt denselben ohnehin geplanten
// "* * * * * php artisan schedule:run"-Cronjob, um die Queue jede Minute kurz abzuarbeiten,
// ohne einen zweiten Cronjob oder einen dauerhaften Prozess zu brauchen.
Schedule::command('queue:work --stop-when-empty --max-time=55 --tries=3')->everyMinute()->withoutOverlapping();
