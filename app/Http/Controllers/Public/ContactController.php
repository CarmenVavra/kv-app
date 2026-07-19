<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreInquiryRequest;
use App\Mail\NewInquiryNotification;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(StoreInquiryRequest $request): RedirectResponse
    {
        $inquiry = Inquiry::create($request->validated());

        $setting = Setting::current();

        if ($setting->notify_new_inquiries && $setting->email) {
            Mail::to($setting->email)->send(new NewInquiryNotification($inquiry));
        }

        return redirect()->to(route('landing').'#kontakt')
            ->with('status', 'Danke für deine Anfrage! Ich melde mich in Kürze bei dir.');
    }
}
