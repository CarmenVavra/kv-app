@php
    use App\Enums\ExpenseStatus;
    use Illuminate\Support\Facades\Storage;
@endphp
<section class="bg-card border border-border rounded-card overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[640px]">
            <div class="grid grid-cols-[82px_minmax(0,1fr)_110px_120px_140px] gap-2 px-5.5 py-3.5 bg-bg text-[12px] font-extrabold text-text-muted uppercase tracking-wide">
                <div>Datum</div><div>Lieferant / Kategorie</div><div>Betrag</div><div>Status</div><div></div>
            </div>
            @forelse ($expenses as $expense)
                <div class="grid grid-cols-[82px_minmax(0,1fr)_110px_120px_140px] gap-2 px-5.5 py-4 items-center border-t border-border text-sm">
                    <div class="text-text-muted">{{ $expense->date->format('d.m.') }}</div>
                    <div class="min-w-0">
                        <div class="font-bold truncate">{{ $expense->supplier }}</div>
                        <div class="text-text-muted text-[12.5px] truncate">{{ $expense->category->label() }}</div>
                    </div>
                    <div class="font-extrabold">{{ $euro((float) $expense->amount) }}</div>
                    <div>
                        <span
                            class="text-[12px] font-bold px-2.5 py-1.5 rounded-chip whitespace-nowrap"
                            style="background: {{ $expense->status->badgeBg() }}; color: {{ $expense->status->badgeText() }};"
                        >{{ $expense->status->label() }}</span>
                    </div>
                    <div class="justify-self-start flex flex-col gap-1.5 items-start">
                        @if ($expense->receipt_path)
                            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="font-bold text-sm whitespace-nowrap">Beleg →</a>
                        @endif
                        @if ($expense->status !== ExpenseStatus::Bezahlt)
                            <form method="POST" action="{{ route('admin.accounting.expenses.mark-paid', $expense) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-bold text-text-muted underline bg-none border-none cursor-pointer p-0">Als bezahlt markieren</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-text-muted text-sm px-5.5 py-6">Noch keine Eingangsrechnungen erfasst.</p>
            @endforelse
        </div>
    </div>
</section>
