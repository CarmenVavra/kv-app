<section class="bg-card border border-border rounded-card overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[560px]">
            <div class="grid grid-cols-[minmax(0,1fr)_120px_120px_120px] gap-2 px-5.5 py-3.5 bg-bg text-[12px] font-extrabold text-text-muted uppercase tracking-wide">
                <div>Zeitraum</div><div>Einnahmen</div><div>Ausgaben</div><div>Gewinn</div>
            </div>
            @forelse ($quarters as $quarter)
                <div class="grid grid-cols-[minmax(0,1fr)_120px_120px_120px] gap-2 px-5.5 py-4 items-center border-t border-border text-sm">
                    <div class="font-bold">{{ $quarter['label'] }}</div>
                    <div class="font-bold text-secondary">{{ $euro($quarter['income']) }}</div>
                    <div class="font-bold text-accent">{{ $euro($quarter['expenses']) }}</div>
                    <div class="font-extrabold">{{ $euro($quarter['profit']) }}</div>
                </div>
            @empty
                <p class="text-text-muted text-sm px-5.5 py-6">Noch keine Daten für dieses Jahr.</p>
            @endforelse
        </div>
    </div>
</section>

<div class="bg-bg rounded-xl px-4.5 py-3.5 text-[13px] text-[color:var(--color-text-muted-2)] leading-relaxed">
    Für die Einkommensteuererklärung: Gesamtgewinn {{ today()->year }} (bisher) {{ $euro($yearlyProfit) }} — Rücklage
    empfohlen ca. {{ $euro(round($yearlyProfit * config('accounting.steuerruecklage_quote'), 2)) }}.
</div>
