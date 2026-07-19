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

        <form method="POST" action="{{ route('login.store') }}" class="bg-card border border-border rounded-card p-8 flex flex-col gap-4">
            @csrf
            <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                E-Mail
                <input
                    type="email" name="email" required autofocus
                    value="{{ old('email') }}"
                    class="border border-border bg-white rounded-field px-3.5 py-2.5 text-sm font-normal text-text"
                >
            </label>
            @error('email')
                <p class="text-[color:var(--color-error-text)] text-xs font-bold -mt-2">{{ $message }}</p>
            @enderror

            <label class="flex flex-col gap-1.5 text-[13px] font-bold text-text-muted">
                Passwort
                <input
                    type="password" name="password" required
                    class="border border-border bg-white rounded-field px-3.5 py-2.5 text-sm font-normal text-text"
                >
            </label>

            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[color:var(--color-text-muted-2)]">
                <input type="checkbox" name="remember" checked class="accent-primary">
                Angemeldet bleiben
            </label>

            <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field py-3 font-bold text-[14.5px] mt-1">
                Anmelden
            </button>
        </form>

        <a href="{{ route('landing') }}" class="text-center no-underline text-text-muted text-sm font-bold">
            ← Zurück zur Website
        </a>
    </div>
</body>
</html>
