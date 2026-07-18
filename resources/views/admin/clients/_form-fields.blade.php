@php
    /** @var \App\Models\Client|null $client */
    $client ??= null;
@endphp

<div class="grid grid-cols-2 gap-3">
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Hundename
        <input
            type="text" name="dog_name" required maxlength="255"
            value="{{ old('dog_name', $client?->dog_name) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Rasse
        <input
            type="text" name="breed" maxlength="255"
            value="{{ old('breed', $client?->breed) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
</div>

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
    Alter
    <input
        type="text" name="age_text" maxlength="255" placeholder="z.B. 3 Jahre"
        value="{{ old('age_text', $client?->age_text) }}"
        class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text max-w-[140px]"
    >
</label>

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
    Halter/in
    <input
        type="text" name="owner_name" required maxlength="255"
        value="{{ old('owner_name', $client?->owner_name) }}"
        class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
    >
</label>

<div class="grid grid-cols-2 gap-3 mt-3">
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        E-Mail
        <input
            type="email" name="email" maxlength="255"
            value="{{ old('email', $client?->email) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Telefon
        <input
            type="text" name="phone" maxlength="50"
            value="{{ old('phone', $client?->phone) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
</div>

<div class="grid grid-cols-[1fr_120px_1fr] gap-3 mt-3">
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Adresse
        <input
            type="text" name="address_line" maxlength="255"
            value="{{ old('address_line', $client?->address_line) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        PLZ
        <input
            type="text" name="postal_code" maxlength="20"
            value="{{ old('postal_code', $client?->postal_code) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
        Ort
        <input
            type="text" name="city" maxlength="255"
            value="{{ old('city', $client?->city) }}"
            class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
        >
    </label>
</div>

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
    Verhaltensthema
    <select
        name="behavior_topic" required
        class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
    >
        @foreach (\App\Enums\BehaviorTopic::cases() as $topic)
            <option
                value="{{ $topic->value }}"
                @selected(old('behavior_topic', $client?->behavior_topic?->value) === $topic->value)
            >{{ $topic->label() }}</option>
        @endforeach
    </select>
</label>

<label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted mt-3">
    Notizen
    <textarea
        name="dog_notes" rows="3"
        class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text resize-y"
        placeholder="Erste Eindrücke, Besonderheiten…"
    >{{ old('dog_notes', $client?->dog_notes) }}</textarea>
</label>
