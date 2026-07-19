<section class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-4.5 max-w-md">
    <h2 class="font-serif font-semibold text-lg">Fahrtkosten-Rechner</h2>
    <p class="text-text-muted text-[13px] leading-relaxed">
        Amtliches Kilometergeld in Österreich: {{ number_format($ratePerKm, 2, ',', '.') }} € pro gefahrenem
        Kilometer für Hausbesuche.
    </p>

    <form method="POST" action="{{ route('admin.accounting.mileage.store') }}" class="flex flex-col gap-4">
        @csrf
        <label class="flex flex-col gap-1.5 text-sm font-bold text-text-muted">
            Gefahrene Kilometer
            <input
                type="number" step="0.1" min="0.1" name="km" id="mileage-km" value="{{ old('km') }}" required
                placeholder="z.B. 24"
                data-rate="{{ $ratePerKm }}"
                oninput="document.getElementById('mileage-result').textContent = this.value ? (parseFloat(this.value.replace(',', '.')) * parseFloat(this.dataset.rate)).toLocaleString('de-AT', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' €' : '— €';"
                class="border border-border bg-white rounded-field px-3 py-2.5 text-sm font-normal text-text"
            >
        </label>

        <div class="bg-bg rounded-xl px-4.5 py-4 flex justify-between items-center">
            <span class="font-bold text-sm">Erstattungsbetrag</span>
            <span id="mileage-result" aria-live="polite" class="font-serif text-xl text-primary">— €</span>
        </div>

        <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">
            Als Ausgabe erfassen
        </button>
    </form>
</section>
