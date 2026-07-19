<x-layouts.admin title="Anfragen">
    <header class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-serif font-semibold text-2xl">Anfragen</h1>
            <p class="text-text-muted text-sm mt-1">{{ $inquiries->count() }} Anfrage(n)</p>
        </div>
    </header>

    <nav class="flex gap-2.5 flex-wrap" aria-label="Nach Status filtern">
        <a
            href="{{ route('admin.inquiries.index') }}"
            class="border border-border rounded-chip px-4 py-2 font-bold text-sm no-underline {{ ! $activeStatus ? 'bg-primary text-white' : 'bg-card text-text' }}"
        >Alle</a>
        @foreach ($statuses as $status)
            <a
                href="{{ route('admin.inquiries.index', ['status' => $status->value]) }}"
                class="border border-border rounded-chip px-4 py-2 font-bold text-sm no-underline {{ $activeStatus === $status->value ? 'bg-primary text-white' : 'bg-card text-text' }}"
            >{{ $status->label() }}</a>
        @endforeach
    </nav>

    <div class="flex flex-col gap-3">
        @forelse ($inquiries as $inquiry)
            @php
                $prefilledClient = new \App\Models\Client([
                    'dog_name' => $inquiry->dog_name,
                    'owner_name' => $inquiry->name,
                    'email' => $inquiry->email,
                ]);
            @endphp
            <div class="bg-card border border-border rounded-card p-4.5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="min-w-0">
                        <div class="font-bold text-[15px]">{{ $inquiry->name }} @if ($inquiry->dog_name)<span class="text-text-muted font-normal">· Hund: {{ $inquiry->dog_name }}</span>@endif</div>
                        <div class="text-text-muted text-[12.5px]">{{ $inquiry->email }} · {{ $inquiry->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <span
                        class="text-[12px] font-bold px-2.5 py-1.5 rounded-chip whitespace-nowrap"
                        style="background: {{ $inquiry->status->badgeBg() }}; color: {{ $inquiry->status->badgeText() }};"
                    >{{ $inquiry->status->label() }}</span>
                </div>

                @if ($inquiry->interested_package)
                    <span class="bg-bg text-[11px] font-bold px-2.5 py-1 rounded-chip text-[color:var(--color-text-muted-2)] w-fit">Interesse: {{ $inquiry->interested_package }}</span>
                @endif

                <p class="text-sm text-text">{{ $inquiry->message }}</p>

                @if (in_array($inquiry->status, [\App\Enums\InquiryStatus::Neu, \App\Enums\InquiryStatus::Kontaktiert]))
                    <div class="flex gap-2.5 flex-wrap pt-1 border-t border-border mt-1">
                        @if ($inquiry->status === \App\Enums\InquiryStatus::Neu)
                            <form method="POST" action="{{ route('admin.inquiries.mark-contacted', $inquiry) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="border border-border bg-white rounded-field px-3.5 py-2 font-bold text-sm">Kontaktiert markieren</button>
                            </form>
                        @endif
                        <button type="button" onclick="document.getElementById('convert-inquiry-{{ $inquiry->id }}').showModal()" class="bg-primary hover:bg-primary-hover text-white rounded-field px-3.5 py-2 font-bold text-sm">Zu Klient machen</button>
                        <form method="POST" action="{{ route('admin.inquiries.archive', $inquiry) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="border border-border bg-white rounded-field px-3.5 py-2 font-bold text-sm">Archivieren</button>
                        </form>
                    </div>
                @endif
            </div>

            <x-admin.modal id="convert-inquiry-{{ $inquiry->id }}" title="Anfrage zu Klient machen">
                <form method="POST" action="{{ route('admin.inquiries.convert', $inquiry) }}" class="flex flex-col gap-1">
                    @csrf
                    @include('admin.clients._form-fields', ['client' => $prefilledClient])

                    <div class="flex gap-2.5 justify-end mt-4">
                        <button type="button" onclick="document.getElementById('convert-inquiry-{{ $inquiry->id }}').close()" class="border border-border bg-white rounded-field px-4.5 py-2.5 font-bold text-sm">Abbrechen</button>
                        <button type="submit" class="bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">Klient anlegen</button>
                    </div>
                </form>
            </x-admin.modal>
        @empty
            <p class="text-text-muted text-sm">Keine Anfragen in dieser Ansicht.</p>
        @endforelse
    </div>
</x-layouts.admin>
