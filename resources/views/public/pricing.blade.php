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
            <div class="font-serif text-[19px] font-semibold text-text">Erstgespräch &amp; Analyse</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-text">110 €</div>
                <div class="text-[13px] text-text-muted">/ Sitzung</div>
            </div>
            <div class="text-[13px] font-bold text-primary">Dauer: ca. 90 Minuten</div>
            <div class="text-[13.5px] text-text-muted leading-relaxed">Jedes erfolgreiche Training beginnt mit einer genauen Ursachenforschung. Wir analysieren das Problemverhalten direkt bei dir vor Ort in gewohnter Umgebung. Du erhältst sofort erste, leicht umsetzbare Managementmaßnahmen für den Alltag.</div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['90 Minuten vor Ort', 'Verhaltensanalyse & Zielsetzung', 'Managementmaßnahmen', 'Schriftliche Zusammenfassung', 'Voraussetzung für alle Trainingseinheiten'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-text">{!! $checkIcon('#6b8e5a') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Erstberatung#kontakt" class="no-underline text-center bg-white border border-border text-text rounded-field py-3 font-bold text-sm mt-2 hover:bg-bg">Jetzt anfragen</a>
        </div>

        {{-- Trainingspaket 4er (highlight) --}}
        <div class="bg-primary border border-primary rounded-[20px] p-7.5 flex flex-col gap-4 relative">
            <div class="absolute -top-3.5 left-6.5 bg-white text-primary text-[11.5px] font-extrabold px-3 py-1.5 rounded-chip">Beliebt</div>
            <div class="font-serif text-[19px] font-semibold text-white">Paket „Sicher durch den Alltag“</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-white">310 €</div>
                <div class="text-[13px] text-white/85">/ Paket</div>
            </div>
            <div class="text-[13px] font-bold text-white">Umfang: Analyse + 3 Praxisstunden</div>
            <div class="text-[13.5px] text-white/85 leading-relaxed">Für Hundehalter, die eine kontinuierliche Begleitung beim Thema Leinenaggression suchen. Wir arbeiten intensiv an der Orientierung am Halter und tasten uns schrittweise an kontrollierte Hundebegegnungen heran.</div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['1 × 90 Minuten Erstgespräch bei Ihnen vor Ort', '3 × 60 Minuten Training', 'Laufende Anpassung des Trainingsplans', 'WhatsApp-Support zwischen Terminen', 'Vorrang bei der Terminvergabe', '7 % günstiger als Einzeltermine'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-white">{!! $checkIcon('#fff') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Trainingspaket+4er#kontakt" class="no-underline text-center bg-white text-primary rounded-field py-3 font-bold text-sm mt-2 hover:opacity-90">Paket buchen</a>
        </div>

        {{-- Einzelstunde (nur Bestandskunden) --}}
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-[20px] p-7.5 flex flex-col gap-4 relative">
            <div class="absolute -top-3.5 left-6.5 bg-gray-500 text-white text-[11.5px] font-extrabold px-3 py-1.5 rounded-chip uppercase tracking-wide">Nur für Bestandskunden</div>
            <div class="font-serif text-[19px] font-semibold text-text">Die Einzelstunde</div>
            <div class="flex items-baseline gap-1.5">
                <div class="font-serif text-[34px] font-semibold text-text">75 €</div>
                <div class="text-[13px] text-text-muted">/ Sitzung</div>
            </div>
            <div class="text-[13px] font-bold text-gray-500">Dauer: 60 Minuten</div>
            <div class="text-[13.5px] text-text-muted leading-relaxed">Gezieltes Begegnungstraining an wechselnden Orten. Perfekt geeignet, um bereits gelerntes Wissen zu vertiefen. <em>Buchbar ausschließlich nach bereits absolviertem Erstgespräch, da wir ohne fundierte Verhaltensanalyse kein sicheres Training garantieren können.</em></div>
            <div class="flex flex-col gap-2.5 mt-1 flex-1">
                @foreach (['8 × 60 Minuten Training', 'Priorisierte Terminvergabe', 'WhatsApp-Support zwischen Terminen', '20 % günstiger als Einzeltermine'] as $feature)
                    <div class="flex gap-2.5 items-start text-[13.5px] text-text">{!! $checkIcon('#6b8e5a') !!}<span>{{ $feature }}</span></div>
                @endforeach
            </div>
            <a href="{{ route('landing') }}?interesse=Einzelstunde#kontakt" class="no-underline text-center bg-white border border-border text-text rounded-field py-3 font-bold text-sm mt-2 hover:bg-bg">Jetzt anfragen</a>
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
