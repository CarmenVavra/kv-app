@php use App\Enums\InvoiceStatus; @endphp
<section class="bg-card border border-border rounded-card overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[640px]">
            <div class="grid grid-cols-[82px_minmax(0,1fr)_110px_120px_140px] gap-2 px-5.5 py-3.5 bg-bg text-[12px] font-extrabold text-text-muted uppercase tracking-wide">
                <div>Datum</div><div>Klient / Leistung</div><div>Betrag</div><div>Status</div><div></div>
            </div>
            @forelse ($invoices as $invoice)
                <div class="grid grid-cols-[82px_minmax(0,1fr)_110px_120px_140px] gap-2 px-5.5 py-4 items-center border-t border-border text-sm">
                    <div class="text-text-muted">{{ $invoice->issue_date->format('d.m.') }}</div>
                    <div class="min-w-0">
                        <div class="font-bold truncate">{{ $invoice->client->dog_name }} ({{ $invoice->client->owner_name }})</div>
                        <div class="text-text-muted text-[12.5px] truncate">{{ $invoice->number }} · {{ $invoice->lineItems->pluck('description')->implode(', ') }}</div>
                    </div>
                    <div class="font-extrabold">{{ $euro((float) $invoice->total_amount) }}</div>
                    <div>
                        <span
                            class="text-[12px] font-bold px-2.5 py-1.5 rounded-chip whitespace-nowrap"
                            style="background: {{ $invoice->status->badgeBg() }}; color: {{ $invoice->status->badgeText() }};"
                        >{{ $invoice->status->label() }}</span>
                    </div>
                    <div class="justify-self-start">
                        <button type="button" onclick="document.getElementById('preview-invoice-{{ $invoice->id }}').showModal()" class="font-bold text-sm bg-none border-none cursor-pointer p-0">
                            PDF →
                        </button>
                    </div>
                </div>

                <x-admin.modal id="preview-invoice-{{ $invoice->id }}" title="Rechnung {{ $invoice->number }}">
                    <div class="flex flex-col gap-1 text-sm">
                        <div class="text-text-muted text-xs font-bold uppercase tracking-wide">Rechnungsempfänger</div>
                        <div class="font-bold">{{ $invoice->client->dog_name }} ({{ $invoice->client->owner_name }})</div>
                    </div>
                    <div class="flex flex-col gap-1.5 mt-4">
                        @foreach ($invoice->lineItems as $item)
                            <div class="flex justify-between text-sm border-b border-border pb-2">
                                <span>{{ $item->description }}</span>
                                <span class="font-bold">{{ $euro((float) $item->amount) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-extrabold text-base pt-1">
                            <span>Gesamtbetrag</span>
                            <span>{{ $euro((float) $invoice->total_amount) }}</span>
                        </div>
                    </div>
                    <div class="bg-bg rounded-xl px-4 py-3 text-xs text-[color:var(--color-text-muted-2)] mt-4 leading-relaxed">
                        Gemäß § 6 Abs. 1 Z 27 UStG (Kleinunternehmerregelung) wird keine Umsatzsteuer berechnet und ausgewiesen.
                    </div>
                    <div class="flex gap-2.5 items-center mt-4 {{ $invoice->status !== InvoiceStatus::Bezahlt ? 'justify-between' : 'justify-end' }}">
                        @if ($invoice->status !== InvoiceStatus::Bezahlt)
                            <form method="POST" action="{{ route('admin.accounting.invoices.mark-paid', $invoice) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="border border-border bg-white rounded-field px-4 py-2 font-bold text-sm">Als bezahlt markieren</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.accounting.invoices.print', $invoice) }}" target="_blank" class="no-underline bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">
                            Drucken / Als PDF
                        </a>
                    </div>
                </x-admin.modal>
            @empty
                <p class="text-text-muted text-sm px-5.5 py-6">Noch keine Ausgangsrechnungen erfasst.</p>
            @endforelse
        </div>
    </div>
</section>
