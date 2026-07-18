@php
    $weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
@endphp
<x-layouts.admin title="Kalender">
    <header class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-serif font-semibold text-2xl">Kalender</h1>
            <p class="text-text-muted text-sm mt-1">{{ $agenda->count() }} Termine am {{ $selectedDay->translatedFormat('d.m.Y') }}</p>
        </div>
        <button
            type="button"
            onclick="document.getElementById('new-appointment-modal').showModal()"
            class="bg-primary hover:bg-primary-hover text-white rounded-field px-4 py-2.5 font-bold text-sm whitespace-nowrap"
        >+ Neuer Termin</button>
    </header>

    <div class="grid gap-6 items-start grid-cols-1 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]">
        <section class="bg-card border border-border rounded-card p-6 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <a
                    href="{{ route('admin.calendar', ['monat' => $month->copy()->subMonth()->toDateString(), 'tag' => $selectedDay->toDateString()]) }}"
                    class="border border-border bg-white rounded-lg w-8 h-8 flex items-center justify-center font-bold no-underline text-text"
                    aria-label="Vorheriger Monat"
                >‹</a>
                <h2 class="font-serif font-semibold text-lg">{{ $month->translatedFormat('F Y') }}</h2>
                <a
                    href="{{ route('admin.calendar', ['monat' => $month->copy()->addMonth()->toDateString(), 'tag' => $selectedDay->toDateString()]) }}"
                    class="border border-border bg-white rounded-lg w-8 h-8 flex items-center justify-center font-bold no-underline text-text"
                    aria-label="Nächster Monat"
                >›</a>
            </div>

            <div class="grid grid-cols-7 gap-1.5 text-xs font-bold text-text-muted text-center">
                @foreach ($weekdays as $weekday)
                    <div>{{ $weekday }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1.5">
                @foreach ($days as $day)
                    <a
                        href="{{ route('admin.calendar', ['monat' => $month->toDateString(), 'tag' => $day['date']->toDateString()]) }}"
                        class="aspect-square rounded-lg p-1.5 flex flex-col gap-1 no-underline border
                            {{ $day['date']->isSameDay($selectedDay) ? 'bg-primary text-white border-transparent' : 'bg-white border-border text-text' }}
                            {{ ! $day['inMonth'] ? 'opacity-40' : '' }}"
                    >
                        <span class="text-xs font-bold">{{ $day['date']->day }}</span>
                        @if ($day['hasAppointments'])
                            <span class="w-1.5 h-1.5 rounded-full {{ $day['date']->isSameDay($selectedDay) ? 'bg-white' : 'bg-primary' }}"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

        <section class="bg-card border border-border rounded-card p-6 flex flex-col gap-3.5">
            <h2 class="font-serif font-semibold text-lg">{{ $selectedDay->translatedFormat('l, d. F') }}</h2>
            @forelse ($agenda as $appointment)
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-bg">
                    <div class="flex flex-col items-center w-11 flex-shrink-0">
                        <span class="font-extrabold text-[13px]">{{ $appointment->starts_at->format('H:i') }}</span>
                        <span class="text-[10.5px] text-text-muted">{{ $appointment->duration_minutes }} min</span>
                    </div>
                    <span class="w-8 h-8 rounded-full flex-shrink-0" style="background: {{ $appointment->client->color }}" aria-hidden="true"></span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.clients.show', $appointment->client) }}" class="font-bold text-[13.5px] no-underline text-text block truncate">
                            {{ $appointment->client->dog_name }} · {{ $appointment->client->owner_name }}
                        </a>
                        <div class="text-text-muted text-xs truncate">{{ $appointment->type->label() }}</div>
                    </div>
                    <span class="bg-white border border-border text-text text-xs font-bold px-2.5 py-1 rounded-chip flex-shrink-0">
                        {{ $appointment->status->label() }}
                    </span>
                </div>
            @empty
                <p class="text-text-muted text-sm">Keine Termine an diesem Tag.</p>
            @endforelse
        </section>
    </div>

    <x-admin.modal id="new-appointment-modal" title="Neuen Termin anlegen">
        <form method="POST" action="{{ route('admin.appointments.store') }}" class="flex flex-col gap-3">
            @csrf
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Klient
                <select name="client_id" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                    <option value="">– auswählen –</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id', $preselectedClientId) == $client->id)>
                            {{ $client->dog_name }} ({{ $client->owner_name }})
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Terminart
                <select name="type" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                    @foreach (\App\Enums\AppointmentType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </label>

            <div class="grid grid-cols-2 gap-3">
                <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                    Datum &amp; Uhrzeit
                    <input
                        type="datetime-local" name="starts_at" required
                        value="{{ old('starts_at', $selectedDay->format('Y-m-d').'T09:00') }}"
                        class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
                    >
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                    Dauer
                    <select name="duration_minutes" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                        @foreach ([30, 45, 60, 90] as $minutes)
                            <option value="{{ $minutes }}" @selected((int) old('duration_minutes', 60) === $minutes)>{{ $minutes }} Minuten</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Notizen
                <textarea name="notes" rows="3" class="border border-border bg-white rounded-field p-3 text-sm text-text resize-y">{{ old('notes') }}</textarea>
            </label>

            <div class="flex gap-2.5 justify-end mt-2">
                <button type="button" onclick="document.getElementById('new-appointment-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Termin speichern</button>
            </div>
        </form>
    </x-admin.modal>

    @if ($errors->any() || $preselectedClientId)
        <script>document.getElementById('new-appointment-modal').showModal();</script>
    @endif
</x-layouts.admin>
