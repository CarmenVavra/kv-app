@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Hundeverhaltensberatung in Wien.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text font-sans antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-card focus:px-4 focus:py-2 focus:rounded-field">
        Zum Inhalt springen
    </a>

    <header class="border-b border-border bg-card" aria-label="Hauptnavigation">
        <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 no-underline" aria-label="Mein Hund der Rowdy — Startseite">
                <span class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <span class="w-4 h-4 rounded-full bg-bg"></span>
                </span>
                <span class="font-serif font-semibold text-base leading-tight text-text">
                    Mein Hund<br><span class="text-primary italic">der Rowdy</span>
                </span>
            </a>
        </div>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="border-t border-border bg-card mt-16" aria-label="Footer">
        <div class="mx-auto max-w-6xl px-6 py-8 flex flex-wrap items-center justify-between gap-4 text-sm text-text-muted">
            <span>&copy; {{ now()->year }} Mein Hund der Rowdy</span>
            <nav class="flex gap-6" aria-label="Rechtliches">
                <a href="{{ route('legal.impressum') }}" class="text-text-muted hover:text-primary">Impressum</a>
                <a href="{{ route('legal.datenschutz') }}" class="text-text-muted hover:text-primary">Datenschutz</a>
                <a href="{{ route('legal.agb') }}" class="text-text-muted hover:text-primary">AGB</a>
            </nav>
        </div>
    </footer>
</body>
</html>
