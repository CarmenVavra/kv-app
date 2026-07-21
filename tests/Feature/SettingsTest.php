<?php

use App\Models\Setting;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Nur die Authenticate-Middleware wird ausgeschaltet (nicht die ganze "web"-Gruppe) - sonst
// fehlen Session/CSRF/$errors, die die View braucht.

it('shows the settings page with the current values', function () {
    Setting::current()->update(['name' => 'Carmen GREGOR', 'praxisname' => 'Mein Hund der Rowdy']);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertSee('Carmen GREGOR')
        ->assertSee('Mein Hund der Rowdy');
});

it('updates the profile, notification toggles, appointment duration and working days', function () {
    $setting = Setting::current();

    $this->withoutMiddleware(Authenticate::class)
        ->put(route('admin.settings.update'), [
            'name' => 'Carmen GREGOR',
            'email' => 'carmen@gregor.at',
            'phone' => '+43 660 1234567',
            'praxisname' => 'Mein Hund der Rowdy',
            'notify_appointment_reminders' => '1',
            'default_appointment_duration' => '90',
            'working_days' => ['Mo', 'Di'],
        ])
        ->assertRedirect(route('admin.settings.index'));

    $setting->refresh();
    expect($setting->name)->toBe('Carmen GREGOR')
        ->and($setting->email)->toBe('carmen@gregor.at')
        ->and($setting->notify_appointment_reminders)->toBeTrue()
        ->and($setting->notify_new_inquiries)->toBeFalse()
        ->and($setting->notify_weekly_summary)->toBeFalse()
        ->and($setting->default_appointment_duration)->toBe(90)
        ->and($setting->working_days)->toBe(['Mo', 'Di']);
});

it('uploads a profile photo', function () {
    Storage::fake('public');
    $setting = Setting::current();

    $this->withoutMiddleware(Authenticate::class)
        ->put(route('admin.settings.update'), [
            'name' => $setting->name,
            'default_appointment_duration' => 60,
            'photo' => UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg'),
        ])
        ->assertRedirect(route('admin.settings.index'));

    $setting->refresh();
    expect($setting->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($setting->avatar_path);
});

it('rejects updating settings without the required name field', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->put(route('admin.settings.update'), [
            'default_appointment_duration' => 60,
        ])
        ->assertSessionHasErrors(['name']);
});
