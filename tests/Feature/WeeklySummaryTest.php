<?php

use App\Mail\WeeklySummaryMail;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

it('sends the weekly summary when enabled with an email on file', function () {
    Mail::fake();
    Setting::current()->update(['notify_weekly_summary' => true, 'email' => 'carmen@gregor.at']);

    $client = Client::factory()->create();
    Appointment::factory()->for($client)->create(['starts_at' => now()->addDays(2)]);
    Inquiry::factory()->create(['created_at' => now()->subDays(2)]);

    $this->artisan('reports:weekly-summary');

    Mail::assertQueued(WeeklySummaryMail::class, function ($mail) {
        return $mail->hasTo('carmen@gregor.at')
            && $mail->stats['appointmentsThisWeek'] === 1
            && $mail->stats['newInquiriesLastWeek'] === 1;
    });
});

it('does not send the weekly summary when disabled', function () {
    Mail::fake();
    Setting::current()->update(['notify_weekly_summary' => false, 'email' => 'carmen@gregor.at']);

    $this->artisan('reports:weekly-summary');

    Mail::assertNotQueued(WeeklySummaryMail::class);
});

it('does not send the weekly summary without an email on file', function () {
    Mail::fake();
    Setting::current()->update(['notify_weekly_summary' => true, 'email' => null]);

    $this->artisan('reports:weekly-summary');

    Mail::assertNotQueued(WeeklySummaryMail::class);
});
