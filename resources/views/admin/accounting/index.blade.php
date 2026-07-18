@php
    $tabs = [
        'out' => 'Ausgangsrechnungen',
        'in' => 'Eingangsrechnungen',
        'mahnwesen' => 'Mahnwesen',
        'wiederkehrend' => 'Wiederkehrend',
        'fahrtkosten' => 'Fahrtkosten',
        'euer' => 'EA-Rechnung',
        'jahr' => 'Jahresübersicht',
    ];
    $euro = fn (float $value) => number_format($value, 2, ',', '.').' €';
@endphp
<x-layouts.admin title="Buchhaltung">
    <header class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-serif font-semibold text-2xl">Buchhaltung</h1>
            <p class="text-text-muted text-sm mt-1">Kleinunternehmerin gemäß § 6 Abs. 1 Z 27 UStG (AT) · keine Umsatzsteuer ausgewiesen</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.accounting.export', ['tab' => $tab]) }}"
                class="no-underline border border-border bg-card text-text rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap"
            >Export (CSV)</a>
            @if ($tab === 'out')
                <button type="button" onclick="document.getElementById('new-invoice-modal').showModal()" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                    + Neue Rechnung
                </button>
            @elseif ($tab === 'in')
                <button type="button" onclick="document.getElementById('new-expense-modal').showModal()" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                    + Beleg erfassen
                </button>
            @elseif ($tab === 'wiederkehrend')
                <button type="button" onclick="document.getElementById('new-recurring-modal').showModal()" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm whitespace-nowrap">
                    + Neue Vorlage
                </button>
            @endif
        </div>
    </header>

    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Einnahmen (Monat)</div>
            <div class="font-serif text-2xl mt-1.5 text-secondary">{{ $euro($stats['income']) }}</div>
        </div>
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Ausgaben (Monat)</div>
            <div class="font-serif text-2xl mt-1.5 text-accent">{{ $euro($stats['expenses']) }}</div>
        </div>
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Gewinn (Monat)</div>
            <div class="font-serif text-2xl mt-1.5 text-primary">{{ $euro($stats['profit']) }}</div>
        </div>
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Offene Rechnungen</div>
            <div class="font-serif text-2xl mt-1.5">{{ $stats['openCount'] }}</div>
            <div class="text-text-muted text-xs mt-0.5">{{ $euro($stats['openSum']) }} ausstehend</div>
        </div>
    </div>

    <nav class="flex gap-1 flex-wrap border-b border-border" aria-label="Buchhaltungs-Bereiche">
        @foreach ($tabs as $key => $label)
            <a
                href="{{ route('admin.accounting.index', ['tab' => $key]) }}"
                class="no-underline px-1.5 py-3 mr-5 font-extrabold text-sm border-b-[3px] {{ $tab === $key ? 'text-primary border-primary' : 'text-text-muted border-transparent' }}"
                @if ($tab === $key) aria-current="page" @endif
            >{{ $label }}</a>
        @endforeach
    </nav>

    @if ($tab === 'out')
        @include('admin.accounting._outgoing', ['invoices' => $invoices, 'euro' => $euro])
    @elseif ($tab === 'in')
        @include('admin.accounting._incoming', ['expenses' => $expenses, 'euro' => $euro])
    @elseif ($tab === 'mahnwesen')
        @include('admin.accounting._dunning', ['overdue' => $overdue, 'euro' => $euro])
    @elseif ($tab === 'wiederkehrend')
        @include('admin.accounting._recurring', ['templates' => $templates, 'euro' => $euro])
    @elseif ($tab === 'fahrtkosten')
        @include('admin.accounting._fahrtkosten', ['ratePerKm' => $ratePerKm])
    @elseif ($tab === 'euer')
        @include('admin.accounting._euer', ['euer' => $euer, 'euro' => $euro])
    @elseif ($tab === 'jahr')
        @include('admin.accounting._jahr', ['quarters' => $quarters, 'yearlyProfit' => $yearlyProfit, 'euro' => $euro])
    @endif

    <x-admin.modal id="new-invoice-modal" title="Neue Ausgangsrechnung">
        <form method="POST" action="{{ route('admin.accounting.invoices.store') }}" class="flex flex-col gap-3.5">
            @csrf
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Klient
                <select name="client_id" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                    <option value="">– auswählen –</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->dog_name }} ({{ $client->owner_name }})</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Leistung
                <input type="text" name="service" value="{{ old('service') }}" required placeholder="z.B. Erstberatung" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Betrag (€)
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required placeholder="80,00" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <div class="flex gap-2.5 justify-end mt-2">
                <button type="button" onclick="document.getElementById('new-invoice-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Rechnung erstellen</button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.modal id="new-expense-modal" title="Eingangsrechnung erfassen">
        <form method="POST" action="{{ route('admin.accounting.expenses.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3.5">
            @csrf
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Lieferant
                <input type="text" name="supplier" value="{{ old('supplier') }}" required placeholder="z.B. BüroPlus" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Kategorie
                <select name="category" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                    @foreach (\App\Enums\ExpenseCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Datum
                <input type="date" name="date" value="{{ old('date', today()->toDateString()) }}" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Betrag (€)
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required placeholder="32,90" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Beleg (optional)
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" class="border border-dashed border-border rounded-field px-3 py-2.5 text-xs text-text-muted">
            </label>
            <div class="flex gap-2.5 justify-end mt-2">
                <button type="button" onclick="document.getElementById('new-expense-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Beleg speichern</button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.modal id="new-recurring-modal" title="Neue wiederkehrende Vorlage">
        <form method="POST" action="{{ route('admin.accounting.recurring.store') }}" class="flex flex-col gap-3.5">
            @csrf
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Titel
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="z.B. Trainingspaket Rocky (4er-Karte)" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Klient
                <select name="client_id" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                    <option value="">– auswählen –</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->dog_name }} ({{ $client->owner_name }})</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Leistung
                <input type="text" name="description" value="{{ old('description') }}" required placeholder="z.B. Trainingspaket" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                    Betrag (€)
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required placeholder="280,00" class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                    Rhythmus
                    <select name="frequency" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
                        @foreach (\App\Enums\RecurringFrequency::cases() as $frequency)
                            <option value="{{ $frequency->value }}" @selected(old('frequency') === $frequency->value)>{{ $frequency->label() }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
                Nächste Rechnung am
                <input type="date" name="next_run_date" value="{{ old('next_run_date', today()->toDateString()) }}" required class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text">
            </label>
            <div class="flex gap-2.5 justify-end mt-2">
                <button type="button" onclick="document.getElementById('new-recurring-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Vorlage speichern</button>
            </div>
        </form>
    </x-admin.modal>

    @if ($errors->any() && old('service') !== null)
        <script>document.getElementById('new-invoice-modal').showModal();</script>
    @elseif ($errors->any() && old('supplier') !== null)
        <script>document.getElementById('new-expense-modal').showModal();</script>
    @elseif ($errors->any() && old('title') !== null)
        <script>document.getElementById('new-recurring-modal').showModal();</script>
    @endif
</x-layouts.admin>
