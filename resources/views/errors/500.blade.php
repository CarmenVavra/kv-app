<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ein Fehler ist aufgetreten — Mein Hund der Rowdy</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-bg text-text font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center gap-6 px-6 text-center">
        <div class="flex items-center gap-2.5">
            <span class="w-[38px] h-[38px] rounded-xl bg-primary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                <span class="w-4 h-4 rounded-full bg-bg"></span>
            </span>
            <span class="font-serif font-semibold text-base text-text">
                Mein Hund <span class="text-primary italic">der Rowdy</span>
            </span>
        </div>

        <div class="flex flex-col items-center gap-3.5">
            <div class="font-serif font-semibold text-primary leading-none" style="font-size: 96px; letter-spacing: -0.02em;">500</div>
            <div class="font-serif font-semibold text-2xl">Da ist etwas schiefgelaufen</div>
            <p class="text-[color:var(--color-text-muted-2)] text-[15px] leading-relaxed max-w-[380px]">
                Auf unserer Seite ist ein unerwarteter Fehler aufgetreten. Bitte versuch es in ein paar Minuten
                noch einmal.
            </p>
        </div>

        <div class="flex gap-3.5 flex-wrap justify-center">
            <a href="{{ route('landing') }}" class="no-underline bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-xl font-bold text-[14.5px]">Zur Website</a>
        </div>
    </div>
</body>
</html>
