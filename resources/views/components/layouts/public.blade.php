@props(['title' => null, 'description' => null])
@php
    $navLinks = [
        ['label' => 'Leistungen', 'href' => route('landing').'#leistungen'],
        ['label' => 'Preise', 'href' => route('pricing')],
        ['label' => 'Über mich', 'href' => route('landing').'#ueber-mich'],
        ['label' => 'Ratgeber', 'href' => route('landing').'#ratgeber'],
    ];
@endphp
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

    <header class="border-b border-border bg-bg" aria-label="Hauptnavigation">
        <div class="mx-auto max-w-6xl px-6 lg:px-14 py-5 flex items-center justify-between gap-4">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 no-underline flex-shrink-0" aria-label="Mein Hund der Rowdy — Startseite">
                <span class="w-[38px] h-[38px] rounded-xl bg-primary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <span class="w-4 h-4 rounded-full bg-bg"></span>
                </span>
                <span class="font-serif font-semibold text-base leading-tight text-text whitespace-nowrap">
                    Mein Hund <span class="text-primary italic">der Rowdy</span>
                </span>
            </a>

            <nav class="hidden lg:flex items-center gap-5.5 font-bold text-[14.5px] whitespace-nowrap" aria-label="Hauptmenü">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="text-text no-underline hover:text-primary">{{ $link['label'] }}</a>
                @endforeach
                <a href="{{ route('landing') }}#kontakt" class="bg-primary hover:bg-primary-hover text-white no-underline px-5 py-2.5 rounded-field">Termin anfragen</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-text no-underline border border-border hover:bg-card px-4.5 py-2.5 rounded-field">Zum Admin-Bereich</a>
                @else
                    <a href="{{ route('login') }}" class="text-text no-underline border border-border hover:bg-card px-4.5 py-2.5 rounded-field">Admin-Login</a>
                @endauth
            </nav>

            <button
                type="button"
                onclick="document.getElementById('mobile-nav').showModal()"
                class="lg:hidden flex items-center justify-center w-10 h-10 rounded-field border border-border bg-card flex-shrink-0"
                aria-label="Menü öffnen"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </header>

    <dialog id="mobile-nav" class="lg:hidden m-0 w-full max-w-none p-0 border-0 bg-card backdrop:bg-[rgba(58,47,39,0.35)]" style="top: 0; left: 0; right: 0; bottom: auto; height: auto;" aria-label="Mobile Navigation">
        <nav class="flex flex-col gap-1 px-6 py-5" aria-label="Mobile Hauptmenü">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" class="text-text no-underline font-bold py-3 border-b border-border">{{ $link['label'] }}</a>
            @endforeach
            @auth
                <a href="{{ route('admin.dashboard') }}" class="text-text no-underline font-bold py-3 border-b border-border">Zum Admin-Bereich</a>
            @else
                <a href="{{ route('login') }}" class="text-text no-underline font-bold py-3 border-b border-border">Admin-Login</a>
            @endauth
            <a href="{{ route('landing') }}#kontakt" class="bg-primary hover:bg-primary-hover text-white no-underline text-center font-bold py-3.5 rounded-field mt-3">Termin anfragen</a>
        </nav>
    </dialog>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="border-t border-border bg-bg mt-16" aria-label="Footer">
        <div class="mx-auto max-w-6xl px-6 lg:px-14 py-8 flex flex-wrap items-center justify-between gap-4 text-sm text-text-muted">
            <span>&copy; {{ now()->year }} Mein Hund der Rowdy — Carmen GREGOR</span>
            <nav class="flex gap-5" aria-label="Rechtliches">
                <a href="{{ route('legal.impressum') }}" class="text-text-muted no-underline hover:text-primary">Impressum</a>
                <a href="{{ route('legal.agb') }}" class="text-text-muted no-underline hover:text-primary">AGB</a>
                <a href="{{ route('legal.datenschutz') }}" class="text-text-muted no-underline hover:text-primary">Datenschutz</a>
            </nav>
        </div>
    </footer>
</body>
</html>
