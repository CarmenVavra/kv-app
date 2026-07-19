<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientStatus;
use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Inquiry;
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

        $newInquiries = Inquiry::query()
            ->where('status', InquiryStatus::Neu)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'todayAppointments' => $todayAppointments,
            'activeClientsCount' => Client::query()->where('status', ClientStatus::Active)->count(),
            'newInquiries' => $newInquiries,
            'newInquiriesCount' => Inquiry::query()->where('status', InquiryStatus::Neu)->count(),
        ]);
    }
}
