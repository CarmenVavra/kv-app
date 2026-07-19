<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Models\Client;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $inquiries = Inquiry::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'statuses' => InquiryStatus::cases(),
            'activeStatus' => $status,
        ]);
    }

    public function markContacted(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => InquiryStatus::Kontaktiert]);

        return redirect()->back();
    }

    public function convert(StoreClientRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $client = Client::create($request->validated() + [
            'color' => collect(Client::AVATAR_PALETTE)->random(),
        ]);

        $inquiry->update(['status' => InquiryStatus::Konvertiert]);

        return redirect()->route('admin.clients.show', $client);
    }

    public function archive(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => InquiryStatus::Archiviert]);

        return redirect()->back();
    }
}
