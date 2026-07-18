<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #3a2f27; font-size: 12px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        .header-table td { vertical-align: top; }
        .brand { font-size: 16px; font-weight: bold; }
        .brand em { color: #b5651d; font-style: italic; }
        .muted { color: #8a7b6a; }
        .text-right { text-align: right; }
        .recipient { margin-bottom: 26px; }
        .label { color: #8a7b6a; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.03em; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th { border-bottom: 2px solid #3a2f27; text-align: left; font-size: 10px; text-transform: uppercase; color: #8a7b6a; padding-bottom: 8px; }
        table.items th.text-right, table.items td.text-right { text-align: right; }
        table.items td { padding: 10px 0; border-bottom: 1px solid #e7dcc9; }
        .total-row td { font-weight: bold; font-size: 14px; border-bottom: none; padding-top: 12px; }
        .notice { margin-top: 24px; background: #f4ede4; padding: 12px 14px; font-size: 11px; color: #6b5a45; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="brand">Mein Hund <em>der Rowdy</em></div>
                {{-- Absenderadresse ist bis zum Setting-Model (M7) fest hinterlegt --}}
                <div class="muted">Lena Brandt<br>Musterstraße 12<br>1010 Wien</div>
            </td>
            <td class="text-right muted">
                <div style="font-weight: bold; color: #3a2f27; font-size: 13px;">Rechnung {{ $invoice->number }}</div>
                Datum: {{ $invoice->issue_date->format('d.m.Y') }}<br>
                Leistungsdatum: {{ $invoice->issue_date->format('d.m.Y') }}
            </td>
        </tr>
    </table>

    <div class="recipient">
        <div class="label">Rechnungsempfänger</div>
        <div style="font-weight: bold;">{{ $invoice->client->owner_name }}</div>
        @if ($invoice->client->address_line)
            <div>{{ $invoice->client->address_line }}</div>
        @endif
        @if ($invoice->client->postal_code || $invoice->client->city)
            <div>{{ $invoice->client->postal_code }} {{ $invoice->client->city }}</div>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr><th>Leistung</th><th class="text-right">Betrag</th></tr>
        </thead>
        <tbody>
            @foreach ($invoice->lineItems as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ number_format((float) $item->amount, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Gesamtbetrag</td>
                <td class="text-right">{{ number_format((float) $invoice->total_amount, 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="notice">
        Gemäß § 6 Abs. 1 Z 27 UStG (Kleinunternehmerregelung) wird keine Umsatzsteuer berechnet und ausgewiesen.
    </div>
</body>
</html>
