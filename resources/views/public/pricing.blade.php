@php
    $checkIcon = fn (string $color) => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-top:2px;flex-shrink:0;"><path d="M4 12l5 5L20 6"></path></svg>';
@endphp
<x-layouts.public title="Preise & Pakete — Mein Hund der Rowdy" description="Transparente Preise für Erstberatung und Trainingspakete bei Mein Hund der Rowdy.">
    <section class="mx-auto max-w-6xl px-6 lg:px-14 pt-8 pb-5 text-center">
        <div class="font-extrabold text-[13px] text-secondary uppercase tracking-wide">Preise &amp; Pakete</div>
        <h1 class="font-serif text-[38px] font-semibold mt-3" style="text-wrap: balance;">Transparent und fair — passend zu eurem Bedarf</h1>
        <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] max-w-xl mx-auto mt-3.5 leading-relaxed">
            Alle Preise verstehen sich als Kleinunternehmerin gemäß § 6 Abs. 1 Z 27 UStG ohne Umsatzsteuerausweis.
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-6 lg:px-14 pt-6 pb-16 grid gap-6 items-stretch" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        {{-- Erstberatung --}}
        <div class="bg-card border border-border rounded-[20px] p-7.5 flex flex-col gap-4 relative">
            <div class="absolute -top-3.5 left-6.5 bg-primary text-white text-[11.5px] font-extrabold px-3 py-1.5 rounded-chip uppercase tracking-wide">Schritt 1: Die Basis</div>
            <div class="font-serif text-[19px] font-semibold text-text">Das Erstgespräch &amp; Analyse</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-text">110 €</div>
                <div class="text-[13px] text-text-muted">/ Sitzung</div>
            </div>
            <div class="text-[13px] font-bold text-secondary">Dauer: ca. 90 Minuten</div>
            <div class="text-[13.5px] text-text-muted leading-relaxed">Jedes erfolgreiche Training beginnt mit einer genauen Ursachenforschung. Wir analysieren das Problemverhalten direkt bei dir vor Ort in gewohnter Umgebung. Du erhältst sofort erste, leicht umsetzbare Managementmaßnahmen für den Alltag.</div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['90 Minuten vor Ort oder per Video', 'Verhaltensanalyse & Zielsetzung', 'Individueller Trainingsplan', 'Schriftliche Zusammenfassung', 'Voraussetzung für alle Trainingspakete'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-text">{!! $checkIcon('#6b8e5a') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Erstberatung#kontakt" class="no-underline text-center bg-white border border-border text-text rounded-field py-3 font-bold text-sm mt-2 hover:bg-bg">Jetzt anfragen</a>
        </div>

        {{-- Trainingspaket 4er (highlight) --}}
        <div class="bg-primary border border-primary rounded-[20px] p-7.5 flex flex-col gap-4 relative">
            <div class="absolute -top-3.5 left-6.5 bg-white text-primary text-[11.5px] font-extrabold px-3 py-1.5 rounded-chip">Beliebt</div>
            <div class="font-serif text-[19px] font-semibold text-white">Trainingspaket 4er</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-white">320 €</div>
                <div class="text-[13px] text-white/85">/ Paket</div>
            </div>
            <div class="text-[13.5px] text-white/85 leading-relaxed">Vier Folgetermine für kontinuierlichen Fortschritt bei einem konkreten Thema.</div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['4 × 60 Minuten Training', 'Laufende Anpassung des Plans', 'WhatsApp-Support zwischen Terminen', '10 % günstiger als Einzeltermine'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-white">{!! $checkIcon('#fff') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Trainingspaket+4er#kontakt" class="no-underline text-center bg-white text-primary rounded-field py-3 font-bold text-sm mt-2 hover:opacity-90">Paket buchen</a>
        </div>

        {{-- Trainingspaket 8er --}}
        <div class="bg-card border border-border rounded-[20px] p-7.5 flex flex-col gap-4 relative">
            <div class="font-serif text-[19px] font-semibold text-text">Trainingspaket 8er</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-text">580 €</div>
                <div class="text-[13px] text-text-muted">/ Paket</div>
            </div>
            <div class="text-[13.5px] text-text-muted leading-relaxed">Für tiefgreifende Themen wie Aggression oder starke Ängste.</div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['8 × 60 Minuten Training', 'Priorisierte Terminvergabe', 'WhatsApp-Support zwischen Terminen', '20 % günstiger als Einzeltermine'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-text">{!! $checkIcon('#6b8e5a') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Trainingspaket+8er#kontakt" class="no-underline text-center bg-white border border-border text-text rounded-field py-3 font-bold text-sm mt-2 hover:bg-bg">Paket buchen</a>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 lg:px-14 pb-20">
        <div class="bg-card border border-border rounded-2xl p-7.5 flex flex-col gap-4">
            <div class="font-serif text-lg font-semibold">Gut zu wissen</div>
            @foreach ([
                'Die Erstberatung ist verpflichtende Voraussetzung für alle Trainingspakete — erst danach ist eine Buchung möglich.',
                'Hausbesuche innerhalb Wiens: keine Aufpreis. Außerhalb: Kilometergeld von 0,50 €/km.',
                'Absagen sind bis 24 Stunden vorher kostenfrei möglich — danach wird der volle Satz verrechnet.',
                'Alle Pakete sind 12 Monate ab Kaufdatum gültig und übertragbar auf andere Hunde im selben Haushalt.',
            ] as $note)
                <div class="flex gap-3 items-start text-sm text-[color:var(--color-text-muted-2)] leading-relaxed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary mt-2 flex-shrink-0" aria-hidden="true"></span>
                    <div>{{ $note }}</div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
