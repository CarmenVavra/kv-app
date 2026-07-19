@php
    /** @var \App\Models\Setting $setting */
    $weekdayNames = ['Mo' => 'Montag', 'Di' => 'Dienstag', 'Mi' => 'Mittwoch', 'Do' => 'Donnerstag', 'Fr' => 'Freitag', 'Sa' => 'Samstag', 'So' => 'Sonntag'];
@endphp
<x-layouts.admin title="Einstellungen">
    <header>
        <h1 class="font-serif font-semibold text-2xl">Einstellungen</h1>
        <p class="text-text-muted text-sm mt-1">Profil, Praxis und Benachrichtigungen verwalten</p>
    </header>

    @if (session('status'))
        <div class="bg-[color:var(--color-success-bg)] text-[color:var(--color-success-text)] rounded-xl px-4 py-3 font-bold text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6 max-w-[820px]">
        @csrf
        @method('PUT')

        {{-- Profil --}}
        <section class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-4.5">
            <div class="font-serif font-semibold text-lg">Profil</div>

            <div class="flex items-center gap-4">
                @if ($setting->avatar_path)
                    <img src="{{ Storage::disk('public')->url($setting->avatar_path) }}" alt="" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
                @else
                    <span class="w-16 h-16 rounded-full bg-secondary flex-shrink-0" aria-hidden="true"></span>
                @endif
                <label class="border border-border bg-white rounded-field px-4 py-2.5 font-bold text-[13px] cursor-pointer">
                    Foto ändern
                    <input type="file" name="photo" accept="image/png,image/jpeg" class="sr-only">
                </label>
            </div>
            @error('photo') <p class="text-[color:var(--color-error-text)] text-xs font-bold">{{ $message }}</p> @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                    Name
                    <input type="text" name="name" required maxlength="255" value="{{ old('name', $setting->name) }}" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                </label>
                <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                    E-Mail
                    <input type="email" name="email" maxlength="255" value="{{ old('email', $setting->email) }}" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                </label>
                <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                    Telefon
                    <input type="text" name="phone" maxlength="50" value="{{ old('phone', $setting->phone) }}" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                </label>
                <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                    Praxisname
                    <input type="text" name="praxisname" maxlength="255" value="{{ old('praxisname', $setting->praxisname) }}" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                </label>
            </div>
        </section>

        {{-- Benachrichtigungen --}}
        <section class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-1">
            <div class="font-serif font-semibold text-lg mb-2">Benachrichtigungen</div>

            @foreach ([
                ['name' => 'notify_appointment_reminders', 'label' => 'Terminerinnerungen', 'desc' => '1 Stunde vor jedem Termin'],
                ['name' => 'notify_new_inquiries', 'label' => 'Neue Anfragen', 'desc' => 'Bei neuer Erstanfrage benachrichtigen'],
                ['name' => 'notify_weekly_summary', 'label' => 'Wöchentliche Zusammenfassung', 'desc' => 'Übersicht jeden Montagmorgen'],
            ] as $toggle)
                <div class="flex items-center justify-between gap-4 py-3 border-b border-border last:border-b-0">
                    <div>
                        <div class="font-bold text-sm">{{ $toggle['label'] }}</div>
                        <div class="text-text-muted text-[12.5px] mt-0.5">{{ $toggle['desc'] }}</div>
                    </div>
                    <label class="relative inline-flex w-11 h-6.5 flex-shrink-0 cursor-pointer">
                        <input type="checkbox" name="{{ $toggle['name'] }}" value="1" @checked(old($toggle['name'], $setting->{$toggle['name']})) aria-label="{{ $toggle['label'] }}" class="peer sr-only">
                        <span class="absolute inset-0 bg-border rounded-chip peer-checked:bg-primary transition-colors" aria-hidden="true"></span>
                        <span class="absolute top-[3px] left-[3px] w-5 h-5 bg-white rounded-full transition-transform peer-checked:translate-x-[18px]" aria-hidden="true"></span>
                    </label>
                </div>
            @endforeach
        </section>

        {{-- Kalender & Verfügbarkeit --}}
        <section class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-4.5">
            <div class="font-serif font-semibold text-lg">Kalender &amp; Verfügbarkeit</div>

            <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                Standard-Termindauer
                <select name="default_appointment_duration" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text max-w-[220px]">
                    @foreach ([30, 45, 60, 90] as $minutes)
                        <option value="{{ $minutes }}" @selected((int) old('default_appointment_duration', $setting->default_appointment_duration) === $minutes)>{{ $minutes }} Minuten</option>
                    @endforeach
                </select>
            </label>

            <div class="flex flex-col gap-1.5">
                <span class="text-[13px] font-bold text-text-muted">Arbeitstage</span>
                <div class="flex gap-2 flex-wrap">
                    @foreach (\App\Models\Setting::WEEKDAYS as $day)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="working_days[]" value="{{ $day }}" @checked(in_array($day, old('working_days', $setting->working_days), true)) aria-label="{{ $weekdayNames[$day] }}" class="peer sr-only">
                            <span class="w-[38px] h-[38px] rounded-field flex items-center justify-center text-[13px] font-bold bg-bg text-text-muted peer-checked:bg-primary peer-checked:text-white transition-colors" aria-hidden="true">{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.settings.index') }}" class="border border-border bg-white rounded-field px-5 py-2.5 font-bold text-sm no-underline text-text">Abbrechen</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-5 py-2.5 font-bold text-sm">Änderungen speichern</button>
        </div>
    </form>
</x-layouts.admin>
