<x-layouts.admin title="Klienten & Hunde">
    <header class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-serif font-semibold text-2xl">Klienten &amp; Hunde</h1>
            <p class="text-text-muted text-sm mt-1">{{ $clients->count() }} aktive Klienten</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" role="search" class="flex" aria-label="Klienten durchsuchen">
                @if ($activeTopic)
                    <input type="hidden" name="thema" value="{{ $activeTopic }}">
                @endif
                <label for="client-search" class="sr-only">Klient oder Hund suchen</label>
                <input
                    type="search" id="client-search" name="suche" value="{{ request('suche') }}"
                    placeholder="Klient oder Hund suchen…"
                    class="border border-border bg-card rounded-field px-4 py-2.5 text-sm w-60"
                >
            </form>
            <button
                type="button"
                onclick="document.getElementById('new-client-modal').showModal()"
                class="bg-primary hover:bg-primary-hover text-white rounded-field px-4 py-2.5 font-bold text-sm whitespace-nowrap"
            >+ Neuer Klient</button>
        </div>
    </header>

    <nav class="flex gap-2.5 flex-wrap" aria-label="Nach Verhaltensthema filtern">
        <a
            href="{{ route('admin.clients.index', array_filter(['suche' => request('suche')])) }}"
            class="border border-border rounded-chip px-4 py-2 font-bold text-sm no-underline {{ ! $activeTopic ? 'bg-primary text-white' : 'bg-card text-text' }}"
        >Alle</a>
        @foreach ($topics as $topic)
            <a
                href="{{ route('admin.clients.index', array_filter(['thema' => $topic->value, 'suche' => request('suche')])) }}"
                class="border border-border rounded-chip px-4 py-2 font-bold text-sm no-underline {{ $activeTopic === $topic->value ? 'bg-primary text-white' : 'bg-card text-text' }}"
            >{{ $topic->label() }}</a>
        @endforeach
    </nav>

    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        @forelse ($clients as $client)
            <a
                href="{{ route('admin.clients.show', $client) }}"
                class="no-underline text-text bg-card border border-border rounded-card p-4.5 flex flex-col gap-3 hover:bg-[#fff5e8] hover:border-accent"
            >
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl flex-shrink-0" style="background: {{ $client->color }}" aria-hidden="true"></span>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-[15px]">{{ $client->dog_name }}</div>
                        <div class="text-text-muted text-[12.5px]">{{ $client->breed }}{{ $client->age_text ? ', '.$client->age_text : '' }}</div>
                    </div>
                </div>
                <div class="text-sm text-text-muted border-t border-border pt-2.5">
                    Halter/in: <span class="text-text font-bold">{{ $client->owner_name }}</span>
                </div>
                <div class="flex gap-1.5 flex-wrap">
                    <span class="bg-bg text-[11px] font-bold px-2.5 py-1 rounded-chip text-[color:var(--color-text-muted-2)]">
                        {{ $client->behavior_topic->label() }}
                    </span>
                </div>
            </a>
        @empty
            <p class="text-text-muted text-sm col-span-full">Noch keine Klienten erfasst.</p>
        @endforelse
    </div>

    <x-admin.modal id="new-client-modal" title="Neuen Klienten erfassen">
        <form method="POST" action="{{ route('admin.clients.store') }}" class="flex flex-col gap-1">
            @csrf
            @include('admin.clients._form-fields', ['client' => null])

            <div class="flex gap-2.5 justify-end mt-4">
                <button type="button" onclick="document.getElementById('new-client-modal').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Klient speichern</button>
            </div>
        </form>
    </x-admin.modal>

    @if ($errors->any())
        <script>document.getElementById('new-client-modal').showModal();</script>
    @endif
</x-layouts.admin>
