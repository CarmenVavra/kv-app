@php
    $sections = [
        ['title' => '§ 1 Geltungsbereich', 'body' => 'Diese Allgemeinen Geschäftsbedingungen gelten für alle Beratungs- und Trainingsleistungen von Lena Brandt ("Mein Hund der Rowdy") gegenüber ihren Kund:innen.'],
        ['title' => '§ 2 Vertragsschluss', 'body' => 'Der Vertrag kommt durch die verbindliche Terminbuchung über die Praxis-Software oder eine formlose Terminvereinbarung zustande.'],
        ['title' => '§ 3 Leistungen', 'body' => 'Gegenstand der Leistung ist die individuelle Verhaltensberatung und das Training von Hunden. Ein bestimmter Trainingserfolg wird nicht geschuldet, da dieser von der Mitwirkung der Halter:innen abhängt.'],
        ['title' => '§ 4 Termine & Absagen', 'body' => 'Termine können bis 24 Stunden vorher kostenfrei abgesagt oder verschoben werden. Bei späteren Absagen oder Nichterscheinen kann der volle Sitzungspreis berechnet werden.'],
        ['title' => '§ 5 Preise & Zahlung', 'body' => 'Es gelten die zum Zeitpunkt der Buchung kommunizierten Preise. Die Zahlung erfolgt nach Rechnungsstellung per Überweisung, sofern nicht anders vereinbart.'],
        ['title' => '§ 6 Haftung', 'body' => 'Die Halter:innen bleiben während der gesamten Sitzung für ihren Hund verantwortlich. Für Schäden, die durch den Hund verursacht werden, haftet dessen Halter:in im Rahmen der gesetzlichen Bestimmungen.'],
        ['title' => '§ 7 Schlussbestimmungen', 'body' => 'Sollte eine Bestimmung dieser AGB unwirksam sein, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt.'],
    ];
@endphp
<x-layouts.public title="AGB — Mein Hund der Rowdy">
    <section class="mx-auto max-w-3xl px-6 lg:px-14 py-12 flex flex-col gap-8">
        <div>
            <h1 class="font-serif font-semibold text-4xl text-text">Allgemeine Geschäftsbedingungen</h1>
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
