<x-mail::message>
# Neue Anfrage

**{{ $inquiry->name }}** ({{ $inquiry->email }}) hat über das Kontaktformular geschrieben
@if ($inquiry->dog_name)
— Hund: **{{ $inquiry->dog_name }}**
@endif
.

@if ($inquiry->interested_package)
Interesse an: **{{ $inquiry->interested_package }}**
@endif

> {{ $inquiry->message }}

<x-mail::button :url="route('admin.inquiries.index')">
Anfrage ansehen
</x-mail::button>

Mein Hund der Rowdy
</x-mail::message>
