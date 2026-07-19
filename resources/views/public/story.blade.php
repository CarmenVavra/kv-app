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
</x-layouts.public>
