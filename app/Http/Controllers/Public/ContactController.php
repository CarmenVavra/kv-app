<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function __invoke(StoreInquiryRequest $request): RedirectResponse
    {
        Inquiry::create($request->validated());

        return redirect()->to(route('landing').'#kontakt')
            ->with('status', 'Danke für deine Anfrage! Ich melde mich in Kürze bei dir.');
    }
}
