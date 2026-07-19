<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $setting = Setting::current();

        $avatarPath = $request->file('photo')?->store('avatars', 'public') ?? $setting->avatar_path;

        $setting->update($request->safe()->except('photo') + [
            'avatar_path' => $avatarPath,
            'notify_new_inquiries' => $request->boolean('notify_new_inquiries'),
            'notify_appointment_reminders' => $request->boolean('notify_appointment_reminders'),
            'notify_weekly_summary' => $request->boolean('notify_weekly_summary'),
            'working_days' => $request->input('working_days', []),
        ]);

        return redirect()->route('admin.settings.index')->with('status', 'Einstellungen gespeichert.');
    }
}
