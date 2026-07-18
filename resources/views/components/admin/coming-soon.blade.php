@props(['title'])
<x-layouts.admin :title="$title">
    <header>
        <h1 class="font-serif font-semibold text-2xl">{{ $title }}</h1>
        <p class="text-text-muted text-sm mt-1">Diese Seite befindet sich im Aufbau.</p>
    </header>
</x-layouts.admin>
