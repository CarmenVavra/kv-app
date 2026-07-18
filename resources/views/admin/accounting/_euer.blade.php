@php $monthLabel = today()->translatedFormat('F Y'); @endphp
<section class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-1">
    <h2 class="font-serif font-semibold text-lg mb-3">Einnahmen-Ausgaben-Rechnung — {{ $monthLabel }}</h2>

    <div class="flex justify-between py-3 border-b border-border text-[14.5px]">
        <span class="font-bold">Betriebseinnahmen</span>
        <span class="font-extrabold">{{ $euro($euer['income']) }}</span>
    </div>
    <div class="flex justify-between py-3 border-b border-border text-[14.5px]">
        <span class="font-bold">Betriebsausgaben</span>
        <span class="font-extrabold">− {{ $euro($euer['expenses']) }}</span>
    </div>
    <div class="flex justify-between py-3 border-b border-border text-[14.5px]">
        <span class="font-extrabold">Gewinn (vor Steuern)</span>
        <span class="font-extrabold text-secondary">{{ $euro($euer['profit']) }}</span>
    </div>
    <div class="flex justify-between py-3 border-b border-border text-[14.5px]">
        <span>davon Umsatzsteuer</span>
        <span class="text-text-muted">0,00 € (§ 6 Abs. 1 Z 27 UStG)</span>
    </div>
    <div class="flex justify-between py-3 text-[14.5px]">
        <span>Rücklage Einkommensteuer (ca. {{ (int) (config('accounting.steuerruecklage_quote') * 100) }}%)</span>
        <span class="text-text-muted">{{ $euro($euer['taxReserve']) }}</span>
    </div>

    <div class="bg-bg rounded-xl px-4.5 py-3.5 text-[13px] text-[color:var(--color-text-muted-2)] leading-relaxed mt-3">
        Als Kleinunternehmerin gemäß § 6 Abs. 1 Z 27 UStG weist du keine Umsatzsteuer aus (Nettoumsatzgrenze aktuell
        55.000 €/Jahr). Diese Übersicht ersetzt keine Steuerberatung — für die Einkommensteuererklärung mit deiner/deinem
        Steuerberater/in abstimmen.
    </div>
</section>
