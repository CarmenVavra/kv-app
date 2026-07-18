<x-layouts.admin title="Dashboard">
    <header>
        <h1 class="font-serif font-semibold text-2xl">Guten Tag, {{ auth()->user()?->name }} 🐾</h1>
        <p class="text-text-muted text-sm mt-1">{{ today()->translatedFormat('l, d. F Y') }} · {{ $todayAppointments->count() }} Termine heute</p>
    </header>

    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Termine heute</div>
            <div class="font-serif text-[28px] mt-1.5 text-primary">{{ $todayAppointments->count() }}</div>
        </div>
        <div class="bg-card border border-border rounded-card p-4.5">
            <div class="text-text-muted text-[13px] font-bold">Aktive Klienten</div>
            <div class="font-serif text-[28px] mt-1.5">{{ $activeClientsCount }}</div>
        </div>
    </div>

    <section class="bg-card border border-border rounded-card p-5.5 flex flex-col gap-3.5">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-semibold text-lg">Heutige Termine</h2>
            <a href="{{ route('admin.calendar') }}" class="text-sm font-bold no-underline">Kalender öffnen →</a>
        </div>
        @forelse ($todayAppointments as $appointment)
            <div class="flex items-center gap-4 p-3.5 rounded-xl bg-bg">
                <div class="flex flex-col items-center w-14 flex-shrink-0">
                    <span class="font-extrabold text-[15px]">{{ $appointment->starts_at->format('H:i') }}</span>
                    <span class="text-[11px] text-text-muted">{{ $appointment->duration_minutes }} min</span>
                </div>
                <span class="w-11 h-11 rounded-full flex-shrink-0" style="background: {{ $appointment->client->color }}" aria-hidden="true"></span>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.clients.show', $appointment->client) }}" class="font-bold text-[14.5px] no-underline text-text block truncate">
                        {{ $appointment->client->dog_name }} · {{ $appointment->client->owner_name }}
                    </a>
                    <div class="text-text-muted text-[13px] truncate">{{ $appointment->type->label() }}</div>
                </div>
                <span class="bg-white border border-border text-text text-xs font-bold px-3 py-1.5 rounded-chip flex-shrink-0">
                    {{ $appointment->status->label() }}
                </span>
            </div>
        @empty
            <p class="text-text-muted text-sm">Heute sind keine Termine geplant.</p>
        @endforelse
    </section>
</x-layouts.admin>
