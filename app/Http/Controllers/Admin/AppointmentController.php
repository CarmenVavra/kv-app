<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAppointmentRequest;
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
        $appointment = Appointment::create($request->validated() + [
            'status' => AppointmentStatus::Bestaetigt,
        ]);

        return redirect()->route('admin.calendar', ['tag' => $appointment->starts_at->toDateString()]);
    }
}
