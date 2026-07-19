@php
    $interesse = old('interested_package', request()->query('interesse'));
    $packageNote = $interesse && $interesse !== 'Erstberatung'
        ? "Da die Erstberatung Voraussetzung für alle Trainingspakete ist, klären wir dort auch dein Interesse am „{$interesse}“ und planen die weiteren Termine."
        : null;
@endphp
<x-layouts.public title="Mein Hund der Rowdy — Hundeverhaltensberatung in Wien">
    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-6 lg:px-14 pt-12 pb-20 grid grid-cols-1 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] gap-10 lg:gap-14 items-center">
        <div class="flex flex-col gap-5 min-w-0">
            <div class="inline-flex items-center gap-2 bg-card border border-border rounded-chip px-3.5 py-1.5 text-[13px] font-bold text-secondary w-fit">
                🐾 Gewaltfreie Hundeverhaltensberatung
            </div>
            <h1 class="font-serif font-bold text-text" style="font-size: clamp(30px, 5vw, 52px); line-height: 1.1; text-wrap: balance;">
                Ein entspannter Hund beginnt mit Verständnis.
            </h1>
            <p class="slogan">„Weil auch ein Rowdy nur verstanden werden will.“</p>

            <p class="text-[17px] leading-relaxed text-[color:var(--color-text-muted-2)] max-w-[480px]">
                Jeder Spaziergang ist ein Spießrutenlauf und dein Hund mutiert an der Leine zum rüpelhaften Rowdy? Schluss mit veralteten Methoden und Frust. Als spezialisierte Verhaltensberaterin helfe ich dir bei Artgenossenunverträglichkeit – wissenschaftlich fundiert, empathisch und garantiert gewaltfrei.
            </p>
            <div class="flex gap-3.5 flex-wrap mt-1">
                <a href="#kontakt" class="bg-primary hover:bg-primary-hover text-white no-underline px-6.5 py-4 rounded-xl font-bold text-[15px]">Kostenloses Erstgespräch</a>
                <a href="#leistungen" class="bg-card border border-border text-text no-underline px-6.5 py-4 rounded-xl font-bold text-[15px] hover:bg-bg">Leistungen ansehen</a>
            </div>
            <div class="flex gap-7 mt-3">
                <div>
                    <div class="font-serif text-2xl font-semibold text-primary">10+</div>
                    <div class="text-[12.5px] font-bold text-text-muted">Jahre Erfahrung</div>
                </div>
                <div>
                    <div class="font-serif text-2xl font-semibold text-primary">400+</div>
                    <div class="text-[12.5px] font-bold text-text-muted">begleitete Hunde</div>
                </div>
                <div>
                    <div class="font-serif text-2xl font-semibold text-primary">4.9★</div>
                    <div class="text-[12.5px] font-bold text-text-muted">Bewertung</div>
                </div>
            </div>
        </div>
        <div class="min-w-0">
            <img
                src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80"
                alt="Hundetraining im Freien"
                class="w-full aspect-[4/5] object-cover rounded-[28px] block max-w-[420px] mx-auto lg:max-w-none"
            >
        </div>
    </section>

    {{-- Leistungen --}}
    <section id="leistungen" class="bg-card border-y border-border py-18 px-6 lg:px-14">
        <div class="mx-auto max-w-6xl">
            <div class="text-center max-w-xl mx-auto mb-11">
                <div class="font-extrabold text-[13px] text-secondary uppercase tracking-wide">Leistungen</div>
                <h2 class="font-serif text-[32px] font-semibold mt-2.5">Beratung, die zu eurem Alltag passt</h2>
            </div>
            <div class="grid gap-5.5" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                    </div>
                    <div class="font-serif font-semibold text-[17px]">Erstberatung</div>
                    <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Gemeinsame Analyse eures Alltags und erste konkrete Schritte.</div>
                </div>
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="w-11 h-11 rounded-xl bg-secondary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 18c3-6 5-9 8-9s5 3 8 9"></path><circle cx="4" cy="19" r="1.4" fill="#fff" stroke="none"></circle><circle cx="20" cy="19" r="1.4" fill="#fff" stroke="none"></circle></svg>
                    </div>
                    <div class="font-serif font-semibold text-[17px]">Leinenaggression</div>
                    <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Gezieltes Training bei Reaktivität und Aggression an der Leine.</div>
                </div>
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="w-11 h-11 rounded-xl bg-accent flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 8.6a4.6 4.6 0 0 0-7.8-3.3L12 6.2l-1-1A4.6 4.6 0 0 0 3.2 8.6c0 2.5 2 4.5 5.4 7.6l3.4 3 3.4-3c3.4-3.1 5.4-5.1 5.4-7.6z"></path></svg>
                    </div>
                    <div class="font-serif font-semibold text-[17px]">Stress</div>
                    <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Dein Fahrplan zu spürbar mehr Gelassenheit.</div>
                </div>
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="w-11 h-11 rounded-xl bg-text-muted flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-6 9 6"></path><path d="M5 9v10h14V9"></path><path d="M9 19v-6h6v6"></path></svg>
                    </div>
                    <div class="font-serif font-semibold text-[17px]">Impulskontrolle</div>
                    <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">In der Ruhe liegt die Kraft - Impulskontrolle ist lernbar.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trainingsansatz --}}
    <section id="trainingsansatz" class="mx-auto max-w-6xl px-6 lg:px-14 py-18">
        <div class="text-center max-w-xl mx-auto mb-11">
            <h2 class="font-serif text-[32px] font-semibold">Mein Trainingsansatz</h2>
        </div>
        <div class="grid gap-5.5" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <div class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-3">
                <div class="font-serif font-semibold text-[17px]">Ursachen statt Symptome</div>
                <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Wir deckeln das Bellen nicht durch Schreckreize, sondern verändern die grundlegende Emotion deines Hundes gegenüber Artgenossen.</div>
            </div>
            <div class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-3">
                <div class="font-serif font-semibold text-[17px]">100% Gewaltfrei</div>
                <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Kein Leinenruck, kein Alphawurf, kein Anschreien. Wir arbeiten auf Basis von Vertrauen, Belohnung und fairer Orientierung am Halter.</div>
            </div>
            <div class="bg-card border border-border rounded-card p-6.5 flex flex-col gap-3">
                <div class="font-serif font-semibold text-[17px]">Wissenschaftlich fundiert</div>
                <div class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed">Modernes Verhaltenstraining nach neuesten kynologischen Erkenntnissen. Ich weiß durch meine Ausbildungen exakt, was im Gehirn deines Hundes vorgeht.</div>
            </div>
        </div>
    </section>

    {{-- Über mich --}}
    <section id="ueber-mich" class="mx-auto max-w-6xl px-6 lg:px-14 py-18 grid grid-cols-1 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)] gap-10 lg:gap-14 items-center">
        <img
            src="https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?auto=format&fit=crop&w=800&q=80"
            alt="Carmen Gregor mit Hund"
            class="w-full aspect-square object-cover rounded-3xl block max-w-[420px] mx-auto lg:max-w-none"
        >
        <div class="flex flex-col gap-4 min-w-0">
            <div class="font-extrabold text-[13px] text-secondary uppercase tracking-wide">Über mich</div>
            <h2 class="font-serif text-[30px] font-semibold">Vom Albtraum zum Vertrauen</h2>
            <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed">
                Ich stand vor Jahren genau am selben Punkt wie du. Ein schlecht informierter Züchter und eine gewalttätige Hundeschule machten meinen Hund zu einem nervösen Bündel, das alles anbellte. Erst als ich selbst 30.000 Euro in meine fundierte Ausbildung investierte, verstand ich die Sprache meines Hundes wirklich. Heute gehen wir entspannt spazieren, weil er mir vertraut.
             </p>
            <a href="{{ route('story') }}" class="inline-flex items-center justify-center border-2 border-secondary text-secondary no-underline px-6 py-3 rounded-full font-bold text-[14.5px] hover:bg-secondary hover:text-white w-fit">Lies meine ganze Geschichte</a>
            <div class="flex gap-2.5 flex-wrap mt-1.5">
                <span class="bg-card border border-border text-[13px] font-bold px-3.5 py-2 rounded-chip">Zert. Hundeverhaltensberaterin</span>
                <span class="bg-card border border-border text-[13px] font-bold px-3.5 py-2 rounded-chip">Fortbildung Lerntheorie</span>
                <span class="bg-card border border-border text-[13px] font-bold px-3.5 py-2 rounded-chip">Mitglied Berufsverband</span>
            </div>
        </div>
    </section>

    {{-- Ratgeber --}}
    <section id="ratgeber" class="bg-card border-y border-border py-18 px-6 lg:px-14">
        <div class="mx-auto max-w-6xl">
            <div class="text-center max-w-xl mx-auto mb-11">
                <div class="font-extrabold text-[13px] text-secondary uppercase tracking-wide">Wissen statt Gewalt</div>
                <h2 class="font-serif text-[32px] font-semibold mt-2.5">Der Rowdy-Ratgeber</h2>
                <p class="text-[color:var(--color-text-muted-2)] text-[15.5px] leading-relaxed mt-3">
                    Hier erfährst du, wie Hunde wirklich lernen, wie Stress das Verhalten beeinflusst und woran du faires Training erkennst.
                </p>
            </div>
            <div class="grid gap-5.5" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="font-extrabold text-[12.5px] text-secondary uppercase tracking-wide">Trainingsmethoden</div>
                    <div class="font-serif font-semibold text-[19px] leading-snug">Warum der Leinenruck die Aggression deines Hundes verschlimmert</div>
                    <p class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed flex-1">Erfahre, welche gefährlichen Fehlverknüpfungen im Gehirn deines Hundes entstehen, wenn mit Schmerz- und Schreckreizen gearbeitet wird.</p>
                    <a href="{{ route('ratgeber.leinenruck') }}" class="text-secondary hover:text-primary no-underline font-bold text-sm mt-1">Weiterlesen →</a>
                </div>
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="font-extrabold text-[12.5px] text-secondary uppercase tracking-wide">Hundesprache</div>
                    <div class="font-serif font-semibold text-[19px] leading-snug">Stresssignale richtig deuten: Höre auf das Flüstern deines Hundes</div>
                    <p class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed flex-1">Bevor ein Hund in die Leine springt, sendet er feine Signale. Wer Lippenlecken, Gähnen und Einfrieren rechtzeitig erkennt, kann Eskalationen verhindern.</p>
                    <a href="{{ route('ratgeber.stresssignale') }}" class="text-secondary hover:text-primary no-underline font-bold text-sm mt-1">Weiterlesen →</a>
                </div>
                <div class="bg-bg rounded-2xl p-6.5 flex flex-col gap-3">
                    <div class="font-extrabold text-[12.5px] text-secondary uppercase tracking-wide">Mythos Ecke</div>
                    <div class="font-serif font-semibold text-[19px] leading-snug">Die Dominanz-Theorie: Ein gefährliches Relikt aus alten Zeiten</div>
                    <p class="text-[color:var(--color-text-muted-2)] text-sm leading-relaxed flex-1">Musst du wirklich als Erster durch die Tür gehen? Wir räumen gründlich mit dem Mythos des „Rudelführers“ auf und zeigen moderne Lösungswege.</p>
                    <a href="{{ route('ratgeber.dominanz-mythos') }}" class="text-secondary hover:text-primary no-underline font-bold text-sm mt-1">Weiterlesen →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Kontakt --}}
    <section id="kontakt" class="mx-auto max-w-6xl px-6 lg:px-14 py-18">
        @if (session('status'))
            <div class="bg-[color:var(--color-success-bg)] text-[color:var(--color-success-text)] rounded-2xl px-5 py-4 mb-6 font-bold text-sm">
                {{ session('status') }}
            </div>
        @endif
        <div class="bg-primary rounded-[28px] p-8 lg:p-14 grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-8 lg:gap-10 items-center text-white">
            <div class="flex flex-col gap-3.5 min-w-0">
                <h2 class="font-serif text-[30px] font-semibold" style="text-wrap: balance;">Bereit für ein entspannteres Miteinander?</h2>
                <p class="text-[15px] leading-relaxed opacity-90 max-w-[440px]">
                    Vereinbare ein kostenloses, unverbindliches Erstgespräch — persönlich, per Video oder telefonisch.
                </p>
                @if ($packageNote)
                    <div class="bg-white/15 rounded-field px-3.5 py-3 text-[13.5px] leading-relaxed">{{ $packageNote }}</div>
                @endif
            </div>
            <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-2.5 bg-card rounded-2xl p-5 min-w-0 text-text">
                @csrf
                <input type="hidden" name="interested_package" value="{{ $interesse }}">
                <label class="sr-only" for="contact-name">Dein Name</label>
                <input id="contact-name" name="name" placeholder="Dein Name" value="{{ old('name') }}" class="border border-border rounded-field px-3.5 py-2.5 text-sm font-sans text-text">
                @error('name') <span class="text-[color:var(--color-error-text)] text-xs font-bold">{{ $message }}</span> @enderror

                <label class="sr-only" for="contact-email">E-Mail-Adresse</label>
                <input id="contact-email" type="email" name="email" placeholder="E-Mail-Adresse" value="{{ old('email') }}" class="border border-border rounded-field px-3.5 py-2.5 text-sm font-sans text-text">
                @error('email') <span class="text-[color:var(--color-error-text)] text-xs font-bold">{{ $message }}</span> @enderror

                <label class="sr-only" for="contact-dog-name">Name deines Hundes</label>
                <input id="contact-dog-name" name="dog_name" placeholder="Name deines Hundes" value="{{ old('dog_name') }}" class="border border-border rounded-field px-3.5 py-2.5 text-sm font-sans text-text">
                @error('dog_name') <span class="text-[color:var(--color-error-text)] text-xs font-bold">{{ $message }}</span> @enderror

                <label class="sr-only" for="contact-message">Deine Nachricht</label>
                <textarea id="contact-message" name="message" rows="3" placeholder="Deine Nachricht" class="border border-border rounded-field px-3.5 py-2.5 text-sm font-sans text-text resize-y">{{ old('message', $interesse ? "Ich interessiere mich für: {$interesse}" : '') }}</textarea>
                @error('message') <span class="text-[color:var(--color-error-text)] text-xs font-bold">{{ $message }}</span> @enderror

                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field py-3.5 font-bold text-[14.5px] mt-1">Erstgespräch anfragen</button>
            </form>
        </div>
    </section>
</x-layouts.public>
