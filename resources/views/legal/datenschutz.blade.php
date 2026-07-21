@php
    $sections = [
        ['title' => '1. Verantwortlicher', 'body' => 'Verantwortlich für die Datenverarbeitung ist Carmen GREGOR, Mein Hund der Rowdy, Musterstraße 12, 1010 Wien, carmen@mhdr.at.'],
        ['title' => '2. Erhebung und Speicherung personenbezogener Daten', 'body' => 'Beim Ausfüllen des Kontaktformulars erheben wir Name, E-Mail-Adresse und den Namen deines Hundes, um dein Anliegen zu bearbeiten und ein Erstgespräch zu vereinbaren. Als Klient:in speichern wir zusätzlich Kontaktdaten, Termine, Nachrichten sowie Notizen zu Trainingsverlauf und Verhalten deines Hundes zur Durchführung der Beratung.'],
        ['title' => '3. Zweck der Verarbeitung', 'body' => 'Die Daten werden ausschließlich zur Terminorganisation, Kommunikation und Dokumentation des Beratungsverlaufs verwendet.'],
        ['title' => '4. Rechtsgrundlage', 'body' => 'Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung bzw. vorvertragliche Maßnahmen) sowie Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an einer geordneten Praxisorganisation).'],
        ['title' => '5. Speicherdauer', 'body' => 'Personenbezogene Daten werden gelöscht, sobald sie für die genannten Zwecke nicht mehr erforderlich sind, spätestens jedoch nach Ablauf gesetzlicher Aufbewahrungsfristen (z.B. für Rechnungsunterlagen).'],
        ['title' => '6. Weitergabe an Dritte', 'body' => 'Eine Weitergabe deiner Daten an Dritte erfolgt nicht, außer soweit dies zur Vertragserfüllung notwendig ist (z.B. Steuerberatung) oder gesetzlich vorgeschrieben.'],
        ['title' => '7. Deine Rechte', 'body' => 'Du hast das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit sowie Widerspruch gegen die Verarbeitung deiner Daten. Wende dich hierzu an die oben genannte Kontaktadresse.'],
    ];
@endphp
<x-layouts.public title="Datenschutzerklärung — Mein Hund der Rowdy">
    <section class="mx-auto max-w-3xl px-6 lg:px-14 py-12 flex flex-col gap-8">
        <div>
            <h1 class="font-serif font-semibold text-4xl text-text">Datenschutzerklärung</h1>
            <p class="text-text-muted text-sm mt-2">Stand: Juli 2026</p>
        </div>

        @foreach ($sections as $section)
            <section class="flex flex-col gap-2">
                <h2 class="font-serif font-semibold text-lg">{{ $section['title'] }}</h2>
                <p class="text-[15px] leading-relaxed text-text">{{ $section['body'] }}</p>
            </section>
        @endforeach
    </section>
</x-layouts.public>
