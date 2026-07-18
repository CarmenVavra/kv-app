<section class="bg-card border border-border rounded-card overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[760px]">
            <div class="grid grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_100px_110px_110px_130px] gap-2 px-5.5 py-3.5 bg-bg text-[12px] font-extrabold text-text-muted uppercase tracking-wide">
                <div>Vorlage</div><div>Klient</div><div>Betrag</div><div>Rhythmus</div><div>Nächste</div><div>Status</div>
            </div>
            @forelse ($templates as $template)
                <div class="grid grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_100px_110px_110px_130px] gap-2 px-5.5 py-4 items-center border-t border-border text-sm">
                    <div class="font-bold truncate">{{ $template->title }}</div>
                    <div class="text-text-muted truncate">{{ $template->client->dog_name }} ({{ $template->client->owner_name }})</div>
                    <div class="font-extrabold">{{ $euro((float) $template->amount) }}</div>
                    <div class="text-text-muted text-[13px]">{{ $template->frequency->label() }}</div>
                    <div class="text-text-muted text-[13px]">{{ $template->next_run_date->format('d.m.Y') }}</div>
                    <div class="flex items-center gap-2">
                        <span
                            class="text-[12px] font-bold px-2.5 py-1.5 rounded-chip whitespace-nowrap"
                            style="{{ $template->active ? 'background: var(--color-success-bg); color: var(--color-success-text);' : 'background: var(--color-bg); color: var(--color-text-muted);' }}"
                        >{{ $template->active ? 'Aktiv' : 'Pausiert' }}</span>
                        <form method="POST" action="{{ route('admin.accounting.recurring.toggle-active', $template) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-bold text-text-muted underline bg-none border-none cursor-pointer p-0">
                                {{ $template->active ? 'Pausieren' : 'Aktivieren' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-text-muted text-sm px-5.5 py-6">Noch keine wiederkehrenden Vorlagen erfasst.</p>
            @endforelse
        </div>
    </div>
</section>
