<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayAppointments = Appointment::query()
            ->onDay(today())
            ->with('client')
            ->orderBy('starts_at')
            ->get();

        return view('admin.dashboard', [
            'todayAppointments' => $todayAppointments,
            'activeClientsCount' => Client::query()->where('status', ClientStatus::Active)->count(),
        ]);
    }
}
