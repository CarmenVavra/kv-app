@props(['title' => null])
@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
        ['label' => 'Kalender', 'route' => 'admin.calendar', 'icon' => 'calendar'],
        ['label' => 'Klienten', 'route' => 'admin.clients.index', 'icon' => 'paw'],
        ['label' => 'Buchhaltung', 'route' => 'admin.accounting.index', 'icon' => 'receipt'],
        ['label' => 'Einstellungen', 'route' => 'admin.settings.index', 'icon' => 'settings'],
    ];
    $setting = \App\Models\Setting::current();
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Praxis-Backend' }} — Mein Hund der Rowdy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text font-sans antialiased pb-[66px] md:pb-0">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-card focus:px-4 focus:py-2 focus:rounded-field">
        Zum Inhalt springen
    </a>

    <div class="flex min-h-screen">
        <aside class="hidden md:flex w-[220px] flex-shrink-0 bg-card border-r border-border flex-col p-[18px] gap-7" aria-label="Hauptnavigation">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-1.5 no-underline">
                <span class="w-[38px] h-[38px] rounded-xl bg-primary flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <span class="w-4 h-4 rounded-full bg-bg"></span>
                </span>
                <span class="font-serif font-semibold text-[16.5px] leading-tight text-text">
                    Mein Hund<br><span class="text-primary italic">der Rowdy</span>
                </span>
            </a>

            <nav class="flex flex-col gap-1">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-field font-bold text-sm no-underline text-text hover:bg-bg {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'bg-bg' : '' }}"
                        @if (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')) aria-current="page" @endif
                    >
                        <span class="w-5 h-5 flex-shrink-0" aria-hidden="true">
                            <x-admin.nav-icon :name="$item['icon']" />
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto flex items-center gap-2.5 p-3 bg-bg rounded-xl">
                @if ($setting->avatar_path)
                    <img src="{{ Storage::disk('public')->url($setting->avatar_path) }}" alt="" class="w-[34px] h-[34px] rounded-full flex-shrink-0 object-cover" aria-hidden="true">
                @else
                    <span class="w-[34px] h-[34px] rounded-full bg-secondary flex-shrink-0" aria-hidden="true"></span>
                @endif
                <div class="text-[13px] leading-tight">
                    <div class="font-bold">{{ \Illuminate\Support\Str::beforeLast($setting->name, ' ') }} <span class="uppercase">{{ \Illuminate\Support\Str::afterLast($setting->name, ' ') }}</span></div>
                    <div class="text-text-muted">Verhaltensberaterin für Hunde</div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-field font-bold text-sm no-underline text-text hover:bg-bg">
                    <span class="w-5 h-5 flex-shrink-0" aria-hidden="true">
                        <x-admin.nav-icon name="globe" />
                    </span>
                    <span>Zur Website</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-text-muted hover:text-primary bg-none border-none cursor-pointer px-1.5">
                        Abmelden
                    </button>
                </form>
            </div>
        </aside>

        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-card border-t border-border py-1.5 px-0.5 flex items-center justify-around z-20" aria-label="Hauptnavigation">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex flex-col items-center gap-0.5 no-underline text-text text-[10px] font-bold px-2 py-1.5"
                    aria-label="{{ $item['label'] }}"
                    @if (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')) aria-current="page" @endif
                >
                    <span class="w-5 h-5" aria-hidden="true">
                        <x-admin.nav-icon :name="$item['icon']" />
                    </span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
            <a
                href="{{ route('landing') }}"
                class="flex flex-col items-center gap-0.5 no-underline text-text text-[10px] font-bold px-2 py-1.5"
                aria-label="Zur Website"
            >
                <span class="w-5 h-5" aria-hidden="true">
                    <x-admin.nav-icon name="globe" />
                </span>
                <span>Website</span>
            </a>
        </nav>

        <main id="main" class="flex-1 min-w-0 p-6 md:p-8 flex flex-col gap-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
