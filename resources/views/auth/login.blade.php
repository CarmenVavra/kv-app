<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-Login — Mein Hund der Rowdy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text font-sans antialiased min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-sm flex flex-col gap-7">

        <div class="flex flex-col items-center gap-3">
            <span class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center" aria-hidden="true">
                <span class="w-5 h-5 rounded-full bg-bg"></span>
            </span>
            <p class="font-serif font-semibold text-lg text-center">
                Mein Hund <span class="text-primary italic">der Rowdy</span>
            </p>
            <p class="text-text-muted text-sm">Admin-Login</p>
        </div>

        <div class="bg-card border border-border rounded-card p-8 flex flex-col gap-5">
            <p class="text-sm text-text-muted text-center">
                Die Anmeldung erfolgt sicher über Auth0.
            </p>
            <a
                href="{{ route('login.authorize') }}"
                class="text-center bg-primary hover:bg-primary-hover text-white rounded-field py-3 font-bold text-sm no-underline"
                aria-label="Mit Auth0 anmelden"
            >
                Mit Auth0 anmelden
            </a>
        </div>

        <a href="{{ route('landing') }}" class="text-center no-underline text-text-muted text-sm font-bold">
            ← Zurück zur Website
        </a>
    </div>
</body>
</html>
