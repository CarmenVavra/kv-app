@props(['heading', 'text', 'ctaLabel'])

<div class="mt-12 text-center bg-bg rounded-2xl p-8">
    <h4 class="font-serif font-semibold text-lg mb-1.5">{{ $heading }}</h4>
    <p class="text-[color:var(--color-text-muted-2)] text-sm mb-4">{{ $text }}</p>
    <a href="{{ route('landing') }}#kontakt" class="bg-primary hover:bg-primary-hover text-white no-underline px-6 py-3.5 rounded-xl font-bold text-[14.5px] inline-block">{{ $ctaLabel }}</a>
</div>
