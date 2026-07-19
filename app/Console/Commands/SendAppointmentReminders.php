<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sendet Terminerinnerungen fuer Termine, die innerhalb der naechsten Stunde beginnen';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! Setting::current()->notify_appointment_reminders) {
            $this->info('Terminerinnerungen sind in den Einstellungen deaktiviert.');

            return;
        }

        $appointments = Appointment::query()
            ->whereNull('reminder_sent_at')
            ->whereBetween('starts_at', [now(), now()->addHour()])
            ->with('client')
            ->get();

        foreach ($appointments as $appointment) {
            if (! $appointment->client->email) {
                continue;
            }

            Mail::to($appointment->client->email)->send(new AppointmentReminderMail($appointment));
            $appointment->update(['reminder_sent_at' => now()]);
        }

        $this->info("{$appointments->count()} Terminerinnerung(en) verarbeitet.");
    }
}
