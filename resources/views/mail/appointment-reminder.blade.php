<x-mail::message>
# Terminerinnerung

Hallo {{ $appointment->client->owner_name }},

kurze Erinnerung an Ihren Termin mit **{{ $appointment->client->dog_name }}** heute um
**{{ $appointment->starts_at->format('H:i') }} Uhr** ({{ $appointment->type->label() }}).

Bei Fragen oder falls der Termin nicht passt, melden Sie sich gerne.

Liebe Grüße<br>
Mein Hund der Rowdy
</x-mail::message>
