@php
    $ausbildungen = [
        [
            'title' => 'Ausbildung Hundetrainerin und Verhaltensberaterin für Hunde',
            'subtitle' => 'SzTVT e. U.',
            'nachweise' => [
                ['id' => 'ausbildung-hundetrainerin', 'label' => 'Nachweis HT ansehen', 'file' => 'ausbildung-hundetrainerin.pdf'],
                ['id' => 'ausbildung-verhaltensberaterin', 'label' => 'Nachweis VB ansehen', 'file' => 'ausbildung-verhaltensberaterin.pdf'],
            ],
        ],
        [
            'title' => 'Vertiefende Ausbildung zur Hundetrainerin und Verhaltensberaterin für Hunde',
            'subtitle' => 'Gesundes Tier — Katharina Aberle',
            'nachweise' => [],
        ],
        [
            'title' => 'Ausbildung Mantrailing und Mantrailing-Trainer',
            'subtitle' => 'ÖBDH - Katharina Aberle',
            'nachweise' => [],
        ],
        [
            'title' => 'Ausbildung Ernährungsberaterin für Hunde',
            'subtitle' => 'SzTVT e. U.',
            'nachweise' => [
                ['id' => 'ausbildung-ernaehrungsberaterin', 'label' => 'Nachweis ansehen', 'file' => 'ausbildung-ernaehrungsberaterin.pdf'],
            ],
        ],
        [
            'title' => 'Ausbildung Tierkommunikation',
            'subtitle' => 'SzTVT e. U.',
            'nachweise' => [
                ['id' => 'ausbildung-tierkommunikation', 'label' => 'Nachweis ansehen', 'file' => 'ausbildung-tierkommunikation.pdf'],
            ],
        ],
    ];

    $seminare = [
        [
            'title' => 'Kognitive Fähigkeiten, soziales Lernen beim Hund',
            'subtitle' => 'Seminar bei Prof. Ádám Miklósi',
            'nachweise' => [
                ['id' => 'seminar-kognitive-faehigkeiten', 'label' => 'Nachweis ansehen', 'file' => 'seminar-kognitive-faehigkeiten.pdf'],
            ],
        ],
        [
            'title' => 'Neurobiologie und Stress bei Hunden',
            'subtitle' => 'Fachseminar zur chemischen Steuerung von Angst und Aggression im Gehirn',
            'nachweise' => [],
        ],
        [
            'title' => 'Über 30.000 € Investition in kynologische Fachliteratur & Forschung',
            'subtitle' => 'Ständige Weiterbildung nach den neuesten, wissenschaftlichen Erkenntnissen der Kynologie',
            'nachweise' => [],
        ],
    ];

    $alleNachweise = collect($ausbildungen)->concat($seminare)->flatMap(fn ($item) => $item['nachweise']);
@endphp
<x-layouts.public title="Meine Geschichte — Mein Hund der Rowdy" description="Die Geschichte hinter der Vision von Mein Hund der Rowdy.">
    <section class="mx-auto max-w-6xl px-6 lg:px-14 py-14 lg:py-18 grid grid-cols-1 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)] gap-10 lg:gap-14 items-start">
        <div class="flex flex-col gap-6">
            <img
                src="{{ asset('images/deutscher-pinscher.webp') }}"
                alt="Deutscher Pinscher"
                class="w-full aspect-[4/5] object-cover rounded-3xl block max-w-[420px] mx-auto lg:max-w-none"
            >
            <blockquote class="bg-card rounded-r-2xl px-5 py-4 m-0 italic text-[color:var(--color-text-muted-2)] text-[15px] leading-relaxed max-w-[420px] mx-auto lg:max-w-none" style="border-left: 4px solid var(--color-secondary);">
                „Gewalt unterdrückt Verhalten nur kurzfristig, aber tiefes Verständnis verändert die Emotion nachhaltig.“
            </blockquote>
        </div>

        <div class="flex flex-col gap-4 min-w-0">
            <div class="font-extrabold text-[13px] text-secondary uppercase tracking-wide">Die Geschichte hinter der Vision</div>
            <h1 class="font-serif font-semibold text-text" style="font-size: clamp(28px, 4vw, 40px); line-height: 1.15; text-wrap: balance;">
                Vom Albtraum zum gemeinsamen Vertrauen
            </h1>

            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Mein Weg zur Verhaltensberatung begann mit einem Versprechen, das sich schnell als Trugschluss herausstellte. Ein wenig qualifizierter Züchter versicherte mir damals, dass ein Deutscher Pinscher der ideale Hund für Anfänger sei. Der einzige Rat, den er mir mit auf den Weg gab, war der Besuch einer Hundeschule. Ich vertraute dieser Aussage und suchte eine Trainerin auf, die jedoch nach veralteten Methoden arbeitete.
            </p>
            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Dort hieß es, ich müsse der unumstrittene Rudelführer werden und der Hund müsse bedingungslos gehorchen, notfalls auch mit körperlicher Gewalt und massiver Einschüchterung. Da Gewalt absolut nicht meiner Philosophie entspricht und sich mein Bauchgefühl wehrte, brach ich das Training schließlich ab. Das Resultat dieser Methoden war verheerend. Mein Hund war dauerhaft gestresst und fing an, alles und jeden lauthals anzubellen, was ihm in die Quere kam.
            </p>
            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Zu diesem enormen Stresslevel gesellte sich kurz darauf ein weiteres prägendes Ereignis, als mein Hund in einer Hundezone von zwei Rüden brutal attackiert wurde. Aus dem ursprünglichen Traum vom harmonischen Begleiter entwickelte sich innerhalb kürzester Zeit ein handfester Alltagshorror. Jeder Spaziergang wurde zum stressigen Spießrutenlauf.
            </p>
            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Anstatt aufzugeben, beschloss ich, die Sache selbst in die Hand zu nehmen. Ich investierte über 30.000 Euro in meine Ausbildung, absolvierte mehrere fundierte Lehrgänge rund um das Thema Hundeverhalten, durchforstete die wissenschaftliche Fachliteratur und bildete mich unermüdlich weiter. Ich wollte nicht mehr nur Symptome bekämpfen, sondern die Sprache meines Hundes endlich wirklich verstehen.
            </p>
            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Mit sehr viel Geduld, tiefem Verständnis und einem konsequent gewaltfreien Trainingsansatz haben wir es geschafft. Heute können wir wieder entspannt spazieren gehen. Mein Hund hat gelernt, dass er in schwierigen Situationen alternative Verhaltensweisen wählen kann, und er vertraut meinem Urteilsvermögen. Genau diesen Weg möchte ich nun auch dir und deinem Hund ermöglichen.
            </p>

            <a href="{{ route('landing') }}#kontakt" class="bg-primary hover:bg-primary-hover text-white no-underline px-6.5 py-4 rounded-xl font-bold text-[15px] w-fit mt-2">Lass uns euren Weg starten</a>
        </div>
    </section>

    {{-- Ausbildungen & Fortbildungen --}}
    <section class="mx-auto max-w-6xl px-6 lg:px-14 py-14 lg:py-18">
        <h2 class="font-serif text-[32px] font-semibold text-center mb-11">Fundiertes Wissen für deinen Erfolg</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
            <div>
                <h3 class="font-serif text-xl font-semibold text-secondary mb-5">Ausbildungen</h3>
                <ul class="flex flex-col gap-5">
                    @foreach ($ausbildungen as $item)
                        <li class="flex gap-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="text-primary flex-shrink-0 mt-1" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                            <div class="flex flex-col gap-0.5">
                                <div class="font-bold text-text text-[15.5px] leading-snug">{{ $item['title'] }}</div>
                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[color:var(--color-text-muted-2)] text-sm">
                                    <span>{{ $item['subtitle'] }}</span>
                                    @foreach ($item['nachweise'] as $nachweis)
                                        <button
                                            type="button"
                                            onclick="document.getElementById('cert-{{ $nachweis['id'] }}').showModal()"
                                            class="inline-flex items-center gap-1 font-bold text-secondary hover:text-primary no-underline bg-none border-none cursor-pointer p-0 text-sm"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            {{ $nachweis['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="font-serif text-xl font-semibold text-secondary mb-5">Seminare &amp; Fortbildungen</h3>
                <ul class="flex flex-col gap-5">
                    @foreach ($seminare as $item)
                        <li class="flex gap-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="text-primary flex-shrink-0 mt-1" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                            <div class="flex flex-col gap-0.5">
                                <div class="font-bold text-text text-[15.5px] leading-snug">{{ $item['title'] }}</div>
                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[color:var(--color-text-muted-2)] text-sm">
                                    <span>{{ $item['subtitle'] }}</span>
                                    @foreach ($item['nachweise'] as $nachweis)
                                        <button
                                            type="button"
                                            onclick="document.getElementById('cert-{{ $nachweis['id'] }}').showModal()"
                                            class="inline-flex items-center gap-1 font-bold text-secondary hover:text-primary no-underline bg-none border-none cursor-pointer p-0 text-sm"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            {{ $nachweis['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @foreach ($alleNachweise as $nachweis)
        <dialog id="cert-{{ $nachweis['id'] }}" aria-label="{{ $nachweis['label'] }}" class="bg-card border border-border rounded-card p-0 w-[92vw] h-[92vh] max-w-4xl backdrop:bg-[rgba(58,47,39,0.5)] m-auto overflow-hidden">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between px-5 py-3 border-b border-border flex-shrink-0">
                    <span class="font-serif font-semibold text-[15px]">{{ $nachweis['label'] }}</span>
                    <button type="button" onclick="this.closest('dialog').close()" class="border border-border bg-white rounded-lg w-8 h-8 flex items-center justify-center font-bold" aria-label="Schließen">×</button>
                </div>
                <iframe src="{{ asset('certificates/'.$nachweis['file']) }}" class="w-full flex-1 border-0" title="{{ $nachweis['label'] }}"></iframe>
            </div>
        </dialog>
    @endforeach
</x-layouts.public>
