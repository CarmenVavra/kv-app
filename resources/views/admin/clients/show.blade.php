<x-layouts.admin :title="$client->dog_name">
    <a href="{{ route('admin.clients.index') }}" class="text-sm font-bold no-underline w-fit">← Zurück zu Klienten</a>

    <section class="bg-card border border-border rounded-card p-6 flex items-center gap-5 flex-wrap">
        <span class="w-17 h-17 rounded-2xl flex-shrink-0" style="background: {{ $client->color }}" aria-hidden="true"></span>
        <div class="flex-1 min-w-[220px]">
            <h1 class="font-serif font-semibold text-2xl">{{ $client->dog_name }}</h1>
            <p class="text-text-muted text-sm mt-0.5">
                {{ $client->breed }}{{ $client->age_text ? ', '.$client->age_text : '' }} · Halter/in: {{ $client->owner_name }}
            </p>
            <div class="flex gap-2 flex-wrap mt-2.5">
                <span class="bg-bg text-[11.5px] font-bold px-2.5 py-1.5 rounded-chip text-[color:var(--color-text-muted-2)]">
                    {{ $client->behavior_topic->label() }}
                </span>
            </div>
        </div>
        <div class="flex gap-2.5 flex-wrap">
            @if ($client->email)
                <a href="mailto:{{ $client->email }}" class="no-underline border border-border bg-white text-text rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                    Nachricht senden
                </a>
            @endif
            <a href="{{ route('admin.calendar', ['klient' => $client->id]) }}" class="no-underline bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                Termin buchen
            </a>
            <button type="button" onclick="document.getElementById('edit-client-modal').showModal()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                Bearbeiten
            </button>
            <button type="button" onclick="document.getElementById('delete-client-modal').showModal()" class="border border-border bg-white text-[#b3402a] rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                Löschen
            </button>
        </div>
    </section>

    <div class="grid gap-6 items-start grid-cols-1 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        <section class="bg-card border border-border rounded-card p-6 flex flex-col gap-1">
            <h2 class="font-serif font-semibold text-lg mb-3">Trainingsverlauf</h2>
            @forelse ($pastAppointments as $appointment)
                <div class="flex gap-4 py-3.5 border-t border-border">
                    <div class="w-[70px] flex-shrink-0 text-text-muted text-xs font-bold pt-0.5">
                        {{ $appointment->starts_at->translatedFormat('d. F') }}
                    </div>
                    <div class="w-2.5 flex-shrink-0 flex justify-center pt-1">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $client->color }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-sm">{{ $appointment->type->label() }}</div>
                        @if ($appointment->notes)
                            <div class="text-[color:var(--color-text-muted-2)] text-[13.5px] leading-relaxed mt-0.5">{{ $appointment->notes }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-text-muted text-sm">Noch keine Termine erfasst.</p>
            @endforelse
        </section>

        <div class="flex flex-col gap-6">
            <section class="bg-card border border-border rounded-card p-5.5 flex flex-col gap-3">
                <h2 class="font-serif font-semibold text-base">Kontakt</h2>
                <div class="text-sm text-[color:var(--color-text-muted-2)] flex flex-col gap-1.5">
                    @if ($client->email)<div>📧 {{ $client->email }}</div>@endif
                    @if ($client->phone)<div>📞 {{ $client->phone }}</div>@endif
                    @if ($client->city)<div>📍 {{ $client->city }}</div>@endif
                </div>
            </section>

            <section class="bg-card border border-border rounded-card p-5.5 flex flex-col gap-3">
                <h2 class="font-serif font-semibold text-base">Nächster Termin</h2>
                @if ($nextAppointment)
                    <div class="bg-bg rounded-xl p-3.5 flex flex-col gap-1">
                        <div class="font-extrabold text-sm">{{ $nextAppointment->starts_at->translatedFormat('D, d. F · H:i') }}</div>
                        <div class="text-text-muted text-[13px]">{{ $nextAppointment->type->label() }}</div>
                    </div>
                @else
                    <p class="text-text-muted text-sm">Kein Termin geplant.</p>
                @endif
            </section>

            <section class="bg-card border border-border rounded-card p-5.5 flex flex-col gap-2.5">
                <h2 class="font-serif font-semibold text-base">Notizen</h2>
                <form method="POST" action="{{ route('admin.clients.update-notes', $client) }}" class="flex flex-col gap-2.5">
                    @csrf
                    @method('PATCH')
                    <label for="dog_notes" class="sr-only">Notizen</label>
                    <textarea
                        id="dog_notes" name="dog_notes" rows="5"
                        class="border border-border bg-white rounded-field p-3 text-sm text-text resize-y"
                    >{{ old('dog_notes', $client->dog_notes) }}</textarea>
                    <button type="submit" class="self-end bg-primary hover:bg-primary-hover text-white rounded-field px-4 py-2 font-bold text-sm">
                        Speichern
                    </button>
                </form>
            </section>
        </div>
    </div>

    <x-admin.modal id="edit-client-modal" title="Klient bearbeiten">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="flex flex-col gap-1">
            @csrf
            @method('PUT')
            @include('admin.clients._form-fields')

            <div class="flex gap-2.5 justify-end mt-4">
                <button type="button" onclick="document.getElementById('edit-client-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Speichern</button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.modal id="delete-client-modal" title="Klient löschen?">
        <p class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">
            {{ $client->dog_name }} ({{ $client->owner_name }}) wird inklusive Trainingsverlauf, Notizen und
            Terminhistorie gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <div class="flex gap-2.5 justify-end mt-4">
            <button type="button" onclick="document.getElementById('delete-client-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-[#b3402a] text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Endgültig löschen</button>
            </form>
        </div>
    </x-admin.modal>

    @if ($errors->any())
        <script>document.getElementById('edit-client-modal').showModal();</script>
    @endif
</x-layouts.admin>
