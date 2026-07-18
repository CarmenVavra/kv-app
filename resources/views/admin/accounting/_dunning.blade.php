<div class="bg-bg rounded-xl px-4.5 py-3.5 text-[13px] text-[color:var(--color-text-muted-2)] leading-relaxed">
    In Österreich üblich: erst eine kostenlose Zahlungserinnerung, danach 1.–2. Mahnung. Ab der 2. Mahnung dürfen
    im B2B-Verkehr gesetzliche Verzugszinsen (9,2 % über Basiszinssatz) sowie ein pauschaler Betreibungsschaden
    von 40 € verrechnet werden.
</div>

<section class="bg-card border border-border rounded-card overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[760px]">
            <div class="grid grid-cols-[90px_minmax(0,1fr)_100px_100px_minmax(0,1fr)_190px] gap-2 px-5.5 py-3.5 bg-bg text-[12px] font-extrabold text-text-muted uppercase tracking-wide">
                <div>Nr.</div><div>Klient</div><div>Betrag</div><div>Überfällig</div><div>Letzte Mahnung</div><div></div>
            </div>
            @forelse ($overdue as $entry)
                @php [$invoice, $draft] = [$entry['invoice'], $entry['draft']]; @endphp
                <div class="grid grid-cols-[90px_minmax(0,1fr)_100px_100px_minmax(0,1fr)_190px] gap-2 px-5.5 py-4 items-center border-t border-border text-sm">
                    <div class="text-text-muted font-bold">{{ $invoice->number }}</div>
                    <div class="font-bold truncate">{{ $invoice->client->dog_name }} ({{ $invoice->client->owner_name }})</div>
                    <div class="font-extrabold">{{ $euro((float) $invoice->total_amount) }}</div>
                    <div>
                        <span class="bg-[color:var(--color-error-bg)] text-[color:var(--color-error-text)] text-[12px] font-bold px-2.5 py-1.5 rounded-chip whitespace-nowrap">
                            {{ $draft['daysOverdue'] }} Tage
                        </span>
                    </div>
                    <div class="text-text-muted text-[13px]">
                        @php $lastReminder = $invoice->paymentReminders->sortByDesc('sent_at')->first(); @endphp
                        {{ $lastReminder ? $lastReminder->level->label().' · '.$lastReminder->sent_at->format('d.m.') : 'Keine' }}
                    </div>
                    <div class="justify-self-start">
                        <button type="button" onclick="document.getElementById('reminder-modal-{{ $invoice->id }}').showModal()" class="border border-border bg-white rounded-lg px-3.5 py-2 font-bold text-[13px] whitespace-nowrap">
                            {{ $draft['level']->actionLabel() }}
                        </button>
                    </div>
                </div>

                <x-admin.modal id="reminder-modal-{{ $invoice->id }}" title="{{ $draft['level']->actionLabel() }}">
                    @if (! $invoice->client->email)
                        <p class="text-[13px] text-[color:var(--color-error-text)]">
                            Für {{ $invoice->client->owner_name }} ist keine E-Mail-Adresse hinterlegt — Versand nicht möglich.
                        </p>
                    @endif
                    <p class="text-text-muted text-[13px] mb-3">
                        Wird per E-Mail an <strong class="text-text">{{ $invoice->client->owner_name }}</strong>
                        ({{ $invoice->client->email ?? '–' }}) gesendet — Betreff und Text kannst du vor dem Senden noch anpassen.
                        @if ($draft['fee'] > 0)
                            Verzugszinsen: {{ $euro($draft['interest']) }}, Betreibungsschaden: {{ $euro($draft['fee']) }}.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('admin.accounting.dunning.send', $invoice) }}" class="flex flex-col gap-3">
                        @csrf
                        <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                            Betreff
                            <input type="text" name="subject" value="{{ old('subject', $draft['subject']) }}" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                        </label>
                        <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                            Nachricht
                            <textarea name="body" rows="9" required class="border border-border bg-white rounded-field p-3 text-[13.5px] font-normal text-text resize-y whitespace-pre-wrap">{{ old('body', $draft['body']) }}</textarea>
                        </label>
                        <div class="flex gap-2.5 justify-end mt-1">
                            <button type="button" onclick="document.getElementById('reminder-modal-{{ $invoice->id }}').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                            <button type="submit" @disabled(! $invoice->client->email) class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm disabled:opacity-50">Per E-Mail senden</button>
                        </div>
                    </form>
                </x-admin.modal>
            @empty
                <p class="text-text-muted text-sm px-5.5 py-6">Keine überfälligen Rechnungen. 🎉</p>
            @endforelse
        </div>
    </div>
</section>
