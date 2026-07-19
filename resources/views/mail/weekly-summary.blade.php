<x-mail::message>
# Deine Woche im Überblick

- **{{ $stats['appointmentsThisWeek'] }}** Termine in den nächsten 7 Tagen
- **{{ $stats['newInquiriesLastWeek'] }}** neue Anfragen in der vergangenen Woche
- **{{ $stats['openInvoicesCount'] }}** offene Rechnungen ({{ number_format($stats['openInvoicesSum'], 2, ',', '.') }} €)

<x-mail::button :url="route('admin.dashboard')">
Zum Dashboard
</x-mail::button>

Mein Hund der Rowdy
</x-mail::message>
