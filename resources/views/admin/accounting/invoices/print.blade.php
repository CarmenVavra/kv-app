<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rechnung {{ $invoice->number }} — Mein Hund der Rowdy</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-bg text-text font-sans antialiased p-8 md:p-14">
    <a href="{{ route('admin.accounting.index', ['tab' => 'out']) }}" class="no-print no-underline font-bold text-sm">← Zurück zur Buchhaltung</a>

    <div class="bg-white max-w-2xl mx-auto mt-6 p-10 md:p-14 rounded-sm shadow-sm">
        <div class="flex justify-between items-start gap-6 flex-wrap mb-11">
            <div>
                <div class="font-serif font-semibold text-lg">Mein Hund <span class="text-primary italic">der Rowdy</span></div>
                {{-- Absenderadresse ist bis zur Einstellungen-Seite (M7 / Setting-Model) fest hinterlegt --}}
                <div class="text-text-muted text-[12.5px] mt-1.5 leading-relaxed">
                    Carmen Gregor<br>Musterstraße 12<br>1010 Wien
                </div>
            </div>
            <div class="text-right text-[12.5px] text-text-muted leading-relaxed">
                <div class="font-extrabold text-text text-[15px]">Rechnung {{ $invoice->number }}</div>
                Datum: {{ $invoice->issue_date->format('d.m.Y') }}<br>
                Leistungsdatum: {{ $invoice->issue_date->format('d.m.Y') }}
            </div>
        </div>

        <div class="mb-9">
            <div class="text-text-muted text-[11.5px] font-extrabold uppercase tracking-wide mb-1.5">Rechnungsempfänger</div>
            <div class="font-bold">{{ $invoice->client->owner_name }}</div>
            @if ($invoice->client->address_line)
                <div class="text-sm text-text-muted">{{ $invoice->client->address_line }}</div>
            @endif
            @if ($invoice->client->postal_code || $invoice->client->city)
                <div class="text-sm text-text-muted">{{ $invoice->client->postal_code }} {{ $invoice->client->city }}</div>
            @endif
        </div>

        <div class="grid grid-cols-[1fr_100px] gap-2 border-b-2 border-text pb-2.5 font-extrabold text-xs uppercase tracking-wide text-text-muted">
            <div>Leistung</div><div class="text-right">Betrag</div>
        </div>
        @foreach ($invoice->lineItems as $item)
            <div class="grid grid-cols-[1fr_100px] gap-2 py-4 border-b border-border">
                <div>{{ $item->description }}</div>
                <div class="text-right font-bold">{{ number_format((float) $item->amount, 2, ',', '.') }} €</div>
            </div>
        @endforeach
        <div class="grid grid-cols-[1fr_100px] gap-2 py-3.5 font-extrabold text-base">
            <div>Gesamtbetrag</div>
            <div class="text-right">{{ number_format((float) $invoice->total_amount, 2, ',', '.') }} €</div>
        </div>

        <div class="mt-7 bg-bg rounded-lg px-4 py-3.5 text-[12.5px] text-[color:var(--color-text-muted-2)] leading-relaxed">
            Gemäß § 6 Abs. 1 Z 27 UStG (Kleinunternehmerregelung) wird keine Umsatzsteuer berechnet und ausgewiesen.
        </div>

        <button type="button" onclick="window.print()" class="no-print mt-8 bg-primary hover:bg-primary-hover text-white rounded-field px-4.5 py-2.5 font-bold text-sm">
            Drucken
        </button>
    </div>
</body>
</html>
