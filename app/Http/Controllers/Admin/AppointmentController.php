<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAppointmentRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $month = Carbon::parse($request->query('monat', 'first day of this month'))->startOfMonth();
        $selectedDay = Carbon::parse($request->query('tag', today()->toDateString()));

        $appointmentsInMonth = Appointment::query()
            ->whereBetween('starts_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->starts_at->toDateString());

        $days = [];
        $cursor = $month->copy()->startOfWeek(Carbon::MONDAY);
        $lastCell = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        while ($cursor->lte($lastCell)) {
            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $month->month,
                'isToday' => $cursor->isToday(),
                'hasAppointments' => $appointmentsInMonth->has($cursor->toDateString()),
            ];
            $cursor->addDay();
        }

        $agenda = Appointment::query()
            ->onDay($selectedDay)
            ->with('client')
            ->orderBy('starts_at')
            ->get();

        return view('admin.calendar', [
            'month' => $month,
            'selectedDay' => $selectedDay,
            'days' => $days,
            'agenda' => $agenda,
            'clients' => Client::query()->orderBy('dog_name')->get(),
            'preselectedClientId' => $request->query('klient'),
            'defaultAppointmentDuration' => Setting::current()->default_appointment_duration,
        ]);
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        [$status, $fee] = Appointment::resolveStatusOption($request->validated('status_option'));

        $appointment = Appointment::create(
            collect($request->validated())->except('status_option')->all()
            + ['status' => $status, 'cancellation_fee' => $fee]
        );

        return redirect()->route('admin.calendar', ['tag' => $appointment->starts_at->toDateString()]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        [$status, $fee] = Appointment::resolveStatusOption($request->validated('status_option'));

        $appointment->update(
            collect($request->validated())->except('status_option')->all()
            + ['status' => $status, 'cancellation_fee' => $fee]
        );

        return redirect()->route('admin.calendar', ['tag' => $appointment->starts_at->toDateString()]);
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $day = $appointment->starts_at->toDateString();
        $appointment->delete();

        return redirect()->route('admin.calendar', ['tag' => $day]);
    }

    /**
     * Schnelles Setzen des Termin-Status direkt aus dem Trainingsverlauf des Klientenprofils.
     * "Abgesagt" kommt in zwei Varianten (mit/ohne Stornogebuehr), die hier auf status = Abgesagt
     * + cancellation_fee gemappt werden; jeder andere Status setzt cancellation_fee zurueck auf null.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        // Sobald zu einer Leistung eine Ausgangsrechnung existiert, ist ihr Status gesperrt —
        // eine spaetere Statusaenderung wuerde die bereits verrechnete Leistung inkonsistent machen.
        if ($appointment->invoiceLineItem()->exists()) {
            return redirect()->route('admin.clients.show', $appointment->client)
                ->with('error', 'Der Status kann nicht geändert werden, weil zu dieser Leistung bereits eine Ausgangsrechnung existiert.');
        }

        $validated = $request->validate([
            'status_option' => ['required', 'in:'.implode(',', array_keys(Appointment::statusOptions()))],
        ]);

        [$status, $fee] = Appointment::resolveStatusOption($validated['status_option']);

        $appointment->update(['status' => $status, 'cancellation_fee' => $fee]);

        return redirect()->route('admin.clients.show', $appointment->client);
    }
}
