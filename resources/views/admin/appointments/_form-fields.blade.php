@php
    /** @var \App\Models\Appointment|null $appointment */
    $appointment ??= null;

    // old() ist global fuer die ganze Seite - im Bearbeiten-Modal (mehrere pro Kalender-Tag) wuerde
    // ein alter Wert sonst in jedem Termin-Modal auftauchen, nicht nur im tatsaechlich abgesendeten.
    // Deshalb nur im Neu-Modal (kein $appointment) auf old() zurueckfallen, im Bearbeiten-Modal immer
    // die echten Werte des jeweiligen Termins zeigen.
    $value = fn (string $field, mixed $default) => $appointment ? $default : old($field, $default);
@endphp

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
    Klient
    <select name="client_id" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
        <option value="">– auswählen –</option>
        @foreach ($clients as $client)
            <option value="{{ $client->id }}" @selected($value('client_id', $appointment?->client_id ?? $preselectedClientId ?? null) == $client->id)>
                {{ $client->dog_name }} ({{ $client->owner_name }})
            </option>
        @endforeach
    </select>
</label>

<div class="grid grid-cols-2 gap-3 mt-3">
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Terminart
        <select name="type" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            @foreach (\App\Enums\AppointmentType::cases() as $type)
                <option value="{{ $type->value }}" @selected($value('type', $appointment?->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Leistung
        <select name="behavior_topic" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            @foreach (\App\Enums\BehaviorTopic::cases() as $topic)
                <option value="{{ $topic->value }}" @selected($value('behavior_topic', $appointment?->behavior_topic?->value) === $topic->value)>{{ $topic->label() }}</option>
            @endforeach
        </select>
    </label>
</div>

@if ($appointment)
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
        Status
        <select name="status" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            @foreach (\App\Enums\AppointmentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected($appointment->status->value === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </label>
@endif

<div class="grid grid-cols-2 gap-3 mt-3">
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Datum &amp; Uhrzeit
        <input
            type="datetime-local" name="starts_at" required
            value="{{ $value('starts_at', $appointment?->starts_at?->format('Y-m-d\TH:i') ?? $selectedDay->format('Y-m-d').'T09:00') }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Dauer
        <select name="duration_minutes" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            @foreach ([30, 45, 60, 90] as $minutes)
                <option value="{{ $minutes }}" @selected((int) $value('duration_minutes', $appointment?->duration_minutes ?? $defaultAppointmentDuration) === $minutes)>{{ $minutes }} Minuten</option>
            @endforeach
        </select>
    </label>
</div>

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
    Notizen
    <textarea name="notes" rows="3" class="border border-border bg-white rounded-field p-3 text-sm text-text resize-y">{{ $value('notes', $appointment?->notes) }}</textarea>
</label>
