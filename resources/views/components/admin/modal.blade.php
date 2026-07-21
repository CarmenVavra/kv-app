@props(['id', 'title', 'maxWidth' => 'max-w-md'])
<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" class="bg-card border border-border rounded-card p-7 w-full {{ $maxWidth }} backdrop:bg-[rgba(58,47,39,0.35)] m-auto max-h-[85vh] overflow-y-auto">
    <div class="relative">
        <form method="dialog">
            <button
                type="submit"
                class="absolute top-0 right-0 text-text-muted hover:text-text"
                aria-label="Schließen"
            >&times;</button>
        </form>

        <h2 id="{{ $id }}-title" class="font-serif font-semibold text-lg mb-4">{{ $title }}</h2>

        {{ $slot }}
    </div>
</dialog>
