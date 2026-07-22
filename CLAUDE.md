# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Current state of this repository

The Laravel 12 application is scaffolded and under active milestone-by-milestone development. The full
architecture/data-model/build-order plan lives at `C:\Users\carme\.claude\plans\recursive-floating-rossum.md`
(also mirrored in project memory) — read it before starting a new milestone. Reference material:

- `Description.md` — the project brief from the client (in German). Source of truth for stack, constraints,
  and deployment target.
- `UI_MOCKUPS/meinHundDerRowdy.zip` and `UI_MOCKUPS/meinHundDerRowdy_02.zip` — two versions of the same design
  handoff (the second adds a `screenshots/` folder with PNG renders of each screen), already unzipped into
  `UI_MOCKUPS/meinHundDerRowdy_02/design_handoff_klientenverwaltung/`.

### Local dev commands

- Install deps: `composer install && npm install`
- Start the whole stack (serve + queue:work + schedule:work + Vite dev server): `.\start.ps1` (or
  double-click `start.bat`)
- Stop it: `.\stop.ps1` (or `stop.bat`) — kills the process tree it started via `taskkill /T`, since
  `php artisan serve` spawns a child PHP dev-server process that a plain `Stop-Process` would orphan; it
  also deletes a leftover `public/hot` if `taskkill` killed Vite before Vite could clean it up itself
- Run tests: `php artisan test` (Pest)
- **`start.ps1` runs `npm run dev` (Vite, live-rebuilds on save) — don't rely on a one-off `npm run build`**
  during a work session. `php artisan serve` never rebuilds assets itself, so editing Tailwind classes in a
  Blade file with only `php artisan serve` running leaves `public/build` stale; new utility classes then
  silently don't apply (looks like a CSS/layout bug — cost real time to track down once already, see the
  `<dialog>` centering fix in the M2 commit). For a one-off production-style build: `npm run build`.
- MySQL is **not** managed by these scripts. On this dev machine it runs as the always-on Windows service
  `MySQL80` (not XAMPP's bundled MySQL, which fails to bind port 3306 because of this), shared with several
  other local projects — `start.ps1` only checks connectivity to `127.0.0.1:3306`. The database `mhdr_kv_db`
  already exists on that instance (root/1234, matching `Description.md` and `.env`).

### Scope decision (confirmed with the client, deviates from the mockup)

The mockup's "Nachrichten" screen (two-way chat UI) is **out of scope** — no chat, no client-facing login, no
public reply mechanism. Contact-form submissions become `Inquiry` records that Carmen answers by ordinary email
outside the app. The admin sidebar therefore has 5 items, not 6: Dashboard, Kalender, Klienten, Buchhaltung,
Einstellungen.

The Landingpage's "Stimmen" (testimonials) nav item and section are **also out of scope**, for the same kind
of reason — nobody has a way to author a testimonial (no client accounts, no admin UI for entering one
either), so the mockup's 3 static testimonial cards would just be fake/placeholder content in production.
Drop both the "Stimmen" nav link and the testimonials section entirely when building M6 — don't build an
"admin enters testimonials manually" feature either unless the client asks for it later. Public nav is
therefore: Leistungen, Preise, Über mich, Termin anfragen, Admin-Login (5 items, not 6).

### Data-model decision (deviates from a "normalized" reading of the mockup)

`Client` is **one merged, deliberately denormalized model per dog** — dog fields (name, breed, behavior topic,
notes) and owner/Halter fields (name, email, phone, address) live on the same row. There is no separate `Dog`
model. Confirmed with the client: a training plan belongs to exactly one dog, so an owner with multiple dogs
gets multiple `Client` rows (owner data repeated each time), not one owner row `hasMany` dogs. See the plan
file for the full model list and rationale.

**2026-07-22 — `Appointment` now also carries a "Leistung" (`behavior_topic`, the same `BehaviorTopic` enum
the Klienten form labels "Leistung"), alongside its existing "Terminart" (`AppointmentType`).** The client
asked for a Leistung dropdown in the "Neuer Termin" dialog — these are two orthogonal facets of a session:
*Terminart* is the session format (Erstberatung/Folgetermin/Hausbesuch/Video-Check-in, and it's what drives
`defaultInvoiceAmount()`), *Leistung* is the behavioural topic worked on (Leinenaggression/Stress/…). A dog's
`Client.behavior_topic` is its overall theme; a single appointment can still address a different topic, so the
appointment gets its own field rather than inheriting the client's. The column is `nullable` (pre-existing
appointments predate it) but the form validates it `required` for new/edited appointments, mirroring the
Klienten "Leistung" select (required, no empty option, defaults to the first enum case). The composed
"{Terminart} · {Leistung}" label lives in one place — `Appointment::typeAndTopicLabel()` (falls back to the
bare Terminart for null-topic legacy rows) — and is reused by the Kalender-Agenda, the Klientenprofil
(Trainingsverlauf + "Nächster Termin"), the "offene Leistungen" checkboxes in the Rechnungsdialog, **and** the
generated invoice line-item `description` (`InvoiceController::store()`), so the Leistung the admin picked on a
Termin is visible both when creating the invoice and on the finished invoice/PDF — not just the Terminart. (An
earlier iteration of this note said the Leistung was deliberately *not* folded into the line-item description;
that was reversed the same day when the client reported the just-picked Leistung wasn't visible in the
"Neue Ausgangsrechnung" dialog.)

**2026-07-22 (follow-up) — status is now chosen when *creating* a Termin, default "Ausständig".** The "Neuer
Termin" form previously had no status field and `store()` hardcoded `Bestaetigt`; now the same combined
`status_option` select (Ausständig / Bestätigt / Abgeschlossen / Abgesagt · mit-|keine-Stornogebühr) appears on
both the create and edit forms via `_form-fields.blade.php`, defaulting to `AppointmentStatus::Ausstehend` for
new appointments (client used the Austrian term "Ausständig", so `Ausstehend->label()` was renamed to
"Ausständig" — the enum *value* stays `'ausstehend'`, DB untouched). `status_option` moved up into
`StoreAppointmentRequest` (so `UpdateAppointmentRequest` is now an empty subclass), and `store()` runs it
through the shared `Appointment::resolveStatusOption()` just like `update()`/`updateStatus()`. This lets the
admin back-fill a past session straight to Abgeschlossen (immediately billable) or record a cancellation with
fee at creation time. The billing side (`scopeUnbilled` = Abgeschlossen or Abgesagt-with-fee) was already in
place from the earlier note and needed no change — verified end-to-end that both statuses show as selectable
checkboxes in the "Neue Ausgangsrechnung" dialog (the Storno one pre-filled to the configured 50 € rate).

**2026-07-22 (follow-up) — each Trainingsverlauf row shows its billing state.** Per past-appointment row on the
Klientenprofil there's now a pill reflecting `$appointment->invoiceLineItem?->invoice?->status`: a muted
outlined "Nicht verrechnet" when no invoice links to it, or a coloured pill "{R-XXXX} · {Bezahlt|Offen|
Überfällig}" (reusing `InvoiceStatus::badgeBg()/badgeText()`, check icon for paid / clock for open) that links
to the invoice's print view. `ClientController::show()` eager-loads `invoiceLineItem.invoice` to avoid an N+1
across the history list (the `Invoice::status` accessor only needs `paid_at`/`due_date`, both on the invoice
row, so the invoice's own `lineItems` don't need loading for this).

### Buchhaltung Kern (M4): Invoice.status is computed, Expense.status is stored

`Invoice` has no `status` column — it's an accessor (`InvoiceStatus` enum: Offen/Bezahlt/Überfällig) derived
from `paid_at` and `due_date`, so there's exactly one place that can go stale (a paid invoice is just one with
`paid_at` set) instead of two. `Expense.status` is the opposite on purpose: a real stored column
(`ExpenseStatus`: Offen/Bezahlt), because unlike an invoice an expense has no `due_date` to derive overdue-ness
from — matches the plan's data-model table. Both models expose a `markPaid()` method used by a small "Als
bezahlt markieren" action (invoice preview modal / expense row) that doesn't exist in the static mockup — added
because without *some* way to flip Offen→Bezahlt, `Invoice.paid_at` (and therefore the Einnahmen/Gewinn stat
tiles, which are cash-basis: only *paid* invoices in the current month count as Einnahmen) could never move
off zero. This is still M4 core CRUD, not the M5 Mahnwesen state machine (no dunning levels, no interest/fee
calculation here).

Invoice numbers (`R-0042` format) come from `App\Services\InvoiceNumberGenerator`, which derives the next
number from the highest existing `R-XXXX` suffix rather than a separate counter column or table `count()`
(safe against gaps from any future invoice deletion). No locking around it — acceptable for a single-admin app
with no concurrent invoice creation.

CSV export (`AccountingController::export()`) uses plain `fputcsv` over a streamed response, no package
dependency, per the plan. Controllers live under `App\Http\Controllers\Admin\Accounting\*`
(`InvoiceController`, `ExpenseController`) per the plan's directory layout, since M5 adds ~5 more controllers
there (Dunning, Recurring, Mileage, Report, InvoicePdf) — `AccountingController` itself (index/export/tab
routing) stays one level up next to `DashboardController`, matching the existing flat convention for
page-level controllers.

The Buchhaltung tab bar always renders all 7 tabs (real `?tab=` query-param routes, not client state, per the
plan). As of M5 all 7 are fully built (Mahnwesen, Wiederkehrend, Fahrtkosten and EA-Rechnung/Jahresübersicht
were the last 4, added on top of M4's Ausgangs-/Eingangsrechnungen).

### Buchhaltung erweitert (M5): Mahnwesen, Wiederkehrend, Fahrtkosten, EÜR, echte PDFs

**Mahnwesen** — `App\Services\DunningService` is the single place that knows the Austrian dunning rules:
`DunningLevel` (Erinnerung → Mahnung1 → Mahnung2, capped — `next()` on the 2nd Mahnung returns itself) is
derived from the invoice's *last* `PaymentReminder`, not stored on the invoice. Verzugszinsen (9,2 % over
Basiszinssatz p.a., § 456 UGB) and the flat 40 € Betreibungsschaden (§ 1333 Abs. 2 ABGB) are computed **only**
when `DunningLevel::chargesInterestAndFee()` is true, i.e. from the 2nd Mahnung onward — never on the free
Erinnerung or the 1st Mahnung. The Basiszinssatz itself fluctuates and isn't fetched live; it's a configurable
approximation in `config/accounting.php` (`BASISZINSSATZ` env var) — update it there if it drifts, don't
hardcode 9,2 % as the *whole* rate anywhere. `DunningService::draft()` pre-fills subject/body for the reminder
modal, but the **actually sent** text is whatever the admin submits in the form (`SendPaymentReminderRequest`)
— `PaymentReminder` stores that submitted text verbatim, not the template, so the sent record and the email
body can never diverge (a plan-mandated test scenario). Sending is blocked server-side with a session error if
the client has no email on file (mockup doesn't show this case since its data is static).

> **2026-07-22 — "Mahnstufe" column on the Ausgangsrechnungen list (`_outgoing.blade.php`, between Status and
> the PDF action):** shows, per invoice, which dunning stage it's at, via `DunningService::currentStage()` —
> which returns the **higher** of (a) the stage *recommended purely from days overdue* and (b) the *highest
> stage actually already sent* (last `PaymentReminder`), so the column never under-reports what the admin has
> already done in the Mahnwesen tab. It's `null` (renders a muted "–") for any invoice that isn't
> `InvoiceStatus::Ueberfaellig` (paid or still within its due date) and for one only a day or two overdue
> (below the Erinnerung threshold). The day thresholds live in `config('accounting.mahnstufe_ab_tag')`
> (Erinnerung ≥ 3, 1. Mahnung ≥ 10, 2. Mahnung ≥ 20 Verzugstage — the lower bound of each range the client
> gave, env-overridable) — this column is **decision support only**; it does not send anything, the actual
> dunning is still triggered manually in the Mahnwesen tab. `DunningLevel` gained `rank()` (for the
> higher-of comparison), `shortLabel()`/`timingHint()` (compact pill text + a `title` tooltip carrying the
> client's per-stage tone/timing description) and `badgeBg()`/`badgeText()` (escalating severity: soft-orange
> warning → soft-red error → solid-red for the final 2. Mahnung, reusing the existing status tokens). The
> controller eager-loads `paymentReminders` alongside `client`/`lineItems` and precomputes an
> `[invoice_id => ?DunningLevel]` map (`$mahnstufen`) so the Blade partial stays logic-free and there's no
> N+1. Reflecting the same staging inside the Mahnwesen tab itself was explicitly deferred by the client
> ("Das können wir nachher machen").

**Wiederkehrend** — `RecurringInvoiceTemplate` + the daily-scheduled `invoices:generate-recurring` command
(`GenerateRecurringInvoices`). Idempotency (plan-mandated: no duplicate on a second same-day run) comes from
calling `$template->advanceNextRunDate()` *immediately* after generating each invoice, inside the same loop
iteration — the next run's `where('next_run_date', '<=', today())` filter then naturally excludes it, so there
is no separate "already ran today" guard column to keep in sync. A "Pausieren/Aktivieren" toggle was added
(mockup only shows a static Aktiv/Pausiert badge) since without one a template could never be paused after
creation.

**Fahrtkosten** — the one place using real client-side JS per the plan ("nur für die Fahrtkosten-Live-Berechnung
gebraucht"): a plain `oninput` handler multiplies km × rate, no jQuery/Ajax needed for a client-side
multiplication. Submitting creates both an `Expense` (category Fahrzeugkosten, status Bezahlt — this is a
reimbursement you're recording for yourself, not something owed to a third party) and a `MileageEntry` row that
snapshots `rate_per_km` at creation time, so a later change to the rate (see the 2026-07-22 note below — this
now lives on `Setting`, not `config/accounting.php`) never rewrites historical entries.

**EÜR/Jahresübersicht** — `App\Services\EuerCalculator` sums on a strict cash basis: an invoice counts as
Einnahmen when `paid_at` falls in the period, never `issue_date` (matches the Kleinunternehmer/Ist-Besteuerung
mental model already used for the M4 stat tiles). Steuerrücklage is a flat ~20 % estimate
(`config('accounting.steuerruecklage_quote')`), explicitly labelled as not tax advice.

**PDFs** — `barryvdh/laravel-dompdf`. Two separate Blade views on purpose, per the plan: `invoices/print.blade.php`
(Tailwind/flexbox, opened in a new tab for the browser's own print dialog) and `invoices/pdf.blade.php`
(plain `<table>`/inline styles only — dompdf's CSS support doesn't reliably handle flexbox/grid). Every invoice
gets a real PDF generated to the **private** `local` disk (`storage/app/private/invoices/{number}.pdf`,
*not* the `public` disk) the moment it's created (`InvoicePdfGenerator`, called from both
`InvoiceController::store()` and `GenerateRecurringInvoices`) — downloads go through an authenticated
controller route rather than a public URL, and `InvoicePdfController::download()` regenerates on demand if the
file is ever missing (e.g. invoices created before this existed). Tests that trigger PDF generation must
`Storage::fake('local')` first, or dompdf writes real files under `storage/app/private/invoices/` during the
test run.

> Note: expense receipt uploads (M4, `ExpenseController::store()`) went to the `public` disk instead, which
> means an uploaded receipt's URL is reachable by anyone who has it, without going through the admin auth guard
> — lower severity than invoice PDFs (no client billing/address data) but worth tightening the same way
> (private disk + authenticated download route) in a follow-up if this becomes a real concern.

**2026-07-22 — "Neue Ausgangsrechnung" now bills a client's open appointments, not a fixed service catalog:**
the client explicitly described an accumulating, per-client set of unbilled services ("Ein Klient hat gewisse
offene Leistungen, die noch verrechnet werden müssen") — the only existing domain concept that naturally
accumulates multiple pending-billing items per client is `Appointment`, so "offene Leistung" is defined by
`Appointment::scopeUnbilled()`, not a manually curated list. This replaced an earlier, narrower iteration of
this same dialog (a single `App\Enums\InvoiceService` dropdown with one fixed line item) — that enum has been
deleted outright rather than kept alongside the new appointment-based flow, since a static catalog and a live
per-client open-items list are two different answers to the same question and only one can be true.

> **2026-07-22 (later same day) — `scopeUnbilled()` no longer requires `status = Abgeschlossen`.** It was
> originally `status = Abgeschlossen AND whereDoesntHave('invoiceLineItem')` (the interpretation confirmed via
> AskUserQuestion). A first fix loosened it to "past + not cancelled + unbilled" because the client left every
> appointment at the creation default `Bestaetigt` and never flipped it, so nothing was ever billable. **That
> loosening was then reverted the same day** once the client asked for an inline status control (see the note
> below): with a way to set status per row, billing is status-driven again and the date-based rule is gone.
> Current scope: **no linked `InvoiceLineItem` AND (`status = Abgeschlossen` OR (`status = Abgesagt` AND
> `cancellation_fee = true`))** — a rendered session, or a late-cancelled one that owes a Stornogebühr. See the
> Trainingsverlauf-status note below for the full rationale.

> **2026-07-22 (later same day) — inline Termin-Status im Trainingsverlauf + status-driven billing + Storno.**
> The client wanted, per Trainingsverlauf row on the Klientenprofil, to see and set the appointment status
> ("Anfangs sehe ich Bestätigt", changeable to Abgeschlossen / Abgesagt), and to "erst nach erbrachter Leistung
> verrechnen". Implemented as a per-row auto-submitting `<select>` (`AppointmentController::updateStatus`, route
> `admin.appointments.update-status`, PATCH). "Abgesagt" is split into two select options — *mit Stornogebühr* /
> *keine Stornogebühr* — because the fee is the only extra bit a cancellation needs for billing. A new nullable
> `appointments.cancellation_fee` boolean holds it (null unless status = Abgesagt); the controller maps the two
> `abgesagt_*` option values to `status = Abgesagt` + the flag, and resets the flag to null for any other status.
> The 24-hour rule ("Stornogebühr wenn < 24 h vor Trainingsbeginn abgesagt") is shown as a guidance hint under
> the heading — the admin picks the variant, it is **not** auto-computed from `starts_at`, so a fee can be
> waived as goodwill. Because status is now actively maintained, `scopeUnbilled()` went back to status-driven
> (Abgeschlossen, or Abgesagt-with-fee) — the exact "erst nach erbrachter Leistung verrechnen" the client asked
> for; `Bestaetigt`/`Ausstehend` (not yet rendered) and folgenlos-abgesagte appointments are deliberately not
> billable. A billable cancelled appointment shows on the invoice as `Appointment::billingLabel()` →
> "Stornogebühr · {Terminart}" (vs. the normal "{Terminart} · {Leistung}" for a rendered session); its amount
> pre-fills via `Appointment::defaultBillingAmount()` to a **configurable flat Stornogebühr**
> (`Setting.stornogebuehr`, default 50 €, editable in Einstellungen → Tarife alongside Kilometergeld and
> Kurzparkschein) rather than the full session price, and stays editable per line in the dialog. A rendered
> session still defaults to `type->defaultInvoiceAmount()`.

> **2026-07-22 (follow-up) — the two requested refinements.** (1) The Stornogebühr amount became an
> admin-editable `Setting.stornogebuehr` (see above), read by `AccountingController::index` (passed to the
> dialog as `$stornoRate`) and re-read server-side in `InvoiceController::store()` for the fallback amount. (2)
> The mit/ohne-Stornogebühr choice is now **also** on the Kalender "Termin bearbeiten" modal, not just the
> Trainingsverlauf — so a *future* appointment can be cancelled-with-fee before it happens. Both the inline
> Trainingsverlauf control and the full edit form now go through the same `Appointment::statusOptions()` /
> `statusOptionValue()` / `resolveStatusOption()` trio (the last maps a `status_option` string back to
> `[AppointmentStatus, ?bool fee]`), so `AppointmentController::update()` and `updateStatus()` share the mapping.
> `UpdateAppointmentRequest` validates `status_option` (not the raw `status`) accordingly. Gotcha hit while
> wiring the edit modal: `admin/appointments/_form-fields.blade.php` defines a `$value` **closure** at the top
> (`old()`-vs-existing helper); the status `@foreach (... as $value => $label)` clobbered it, so every field
> rendered *after* the status select (`starts_at`, `duration_minutes`) then called the string as a function
> ("Call to undefined function abgesagt_ohne_gebuehr()"). Fixed by naming the loop vars `$statusValue`/
> `$statusLabel` — a reminder not to reuse `$value` as a loop var in that partial.

`InvoiceLineItem` gained a nullable `appointment_id` (not `Invoice` — one invoice can now bill several open
appointments at once, "Ich möchte pro Rechnung mehrere Leistungen erfassen", so the link has to live at the
line-item level; `Invoice`'s own pre-existing unused `appointment_id` column was left alone rather than folded
into this, since removing it wasn't asked for and isn't necessary for this feature to work correctly). The
"Neue Ausgangsrechnung" dialog pre-renders every client's open appointments (grouped, one hidden block per
client) and a small vanilla-JS function swaps which block is visible on `client_id` change — no new AJAX
endpoint, consistent with this app's existing no-SPA/minimal-JS stance, and fine at this app's scale (one admin,
a small client list). Each open appointment is its own checkbox + an editable, type-defaulted amount
(`AppointmentType::defaultInvoiceAmount()`: Erstberatung 90 €, everything else 60 €) so several can be billed
on one invoice; "Sonstige Leistung" is a free description+amount pair that's always available regardless of
whether the selected client has any open appointments, for one-off charges with no matching appointment.
`InvoiceController::store()` re-derives the billable appointment set server-side
(`Appointment::whereIn(...)->where('client_id', ...)->unbilled()`) rather than trusting the submitted
`appointments[]` IDs outright, so a manipulated request can't bill someone else's appointment or double-bill
an already-invoiced one.

Fahrtkosten/Kurzparkschein on this dialog are genuinely new line items, not the M5 Fahrtkosten-Rechner
Expense flow (which stays a separate, self-reimbursement concept) — these bill the *client*. Fahrtkosten is a
plain `gefahrene Kilometer × Kilometergeld` (no automatic round-trip doubling — an earlier iteration of this
same feature doubled a one-way entry for "Hin- und Rückfahrt", but the client's follow-up description was
explicit that the entered km is simply multiplied by the rate, so the admin enters however many km were
actually driven). Kurzparkschein is `Stunden × 2 × Halbstunden-Tarif` — the client was explicit that this
factor-of-2 must not be dropped, since the real Viennese Kurzparkschein tariff is priced per half hour, not
per hour. Both rates (`kilometergeld_pro_km`, `kurzparkschein_tarif_pro_halbe_stunde`) moved from
`config/accounting.php` to `Setting` (Einstellungen → "Tarife") since the client asked for them to be
admin-editable, not just `.env`-editable like the Mahnwesen Basiszinssatz — `App\Services\MileageCalculator`
(used by both this feature and the existing M5 Fahrtkosten-Rechner) and the new `App\Services\ParkingCalculator`
both read from `Setting::current()` now, so a rate change applies everywhere the rate is used, not just one of
the two features that happen to need it.

**2026-07-22 — fixed a wrong Leistungsdatum on the invoice PDF/print view and added a Zahlungsfrist notice:**
both `invoices/print.blade.php` and `invoices/pdf.blade.php` printed `$invoice->issue_date` twice under two
different labels ("Datum" and "Leistungsdatum") — a copy-paste bug, since the service date and the invoice
date are conceptually different and can genuinely diverge (an appointment billed a week or two after it
happened, per the "offene Leistungen" flow above). Fixed by adding `Invoice::service_date_label` (an
accessor): it collects the `starts_at` dates of every line item's linked `Appointment`, shows the single date
when they agree, a "first – last" range when a multi-appointment invoice spans several days, and falls back to
`issue_date` only when no line item is appointment-linked (pure Sonstiges/Fahrtkosten/Kurzparkschein invoice,
which has no other date to anchor to). Both invoice-generating call sites
(`InvoiceController::print()`, `InvoicePdfGenerator::generate()`) now eager-load `lineItems.appointment` to
back this without N+1 queries.

Also added a "Zahlungsziel: 7 Tage" line plus a friendly payment-term sentence citing the concrete due date
("...bis spätestens {due_date}. Vielen Dank für Ihr Vertrauen!") to both views, since neither previously said
anything about a payment deadline at all. This surfaced a latent inconsistency: `due_date` was actually being
computed as `issue_date + 14 days` in both `InvoiceController::store()` and `GenerateRecurringInvoices`, while
`DunningService`'s reminder texts already asked for payment "innerhalb von 7 Tagen" — the two numbers had
never matched. The client confirmed the real payment term is 7 days, so both `+14` call sites were corrected
to `+7`, making `due_date` (and therefore `InvoiceStatus::Ueberfaellig`/Mahnwesen eligibility) consistent with
what the invoice itself now tells the client to expect.

### Öffentliche Website (M6): Landingpage, Preise, Rechtsseiten, Kontaktformular → Inquiry

**Static pages stay `Route::view()`, no controller** — Landingpage, Preise, and the three Rechtsseiten
(Impressum/AGB/Datenschutz) have no server-side logic beyond reading `request()->query('interesse')` directly
in the Landingpage Blade view (Blade always has access to `request()`/`old()`/`$errors`/`session()` regardless
of whether the route is `Route::view()` or a controller action), so a dedicated `LandingController`/
`PricingController`/`LegalController` per the original plan sketch would be pure ceremony. The **only** real
public controller is `App\Http\Controllers\Public\ContactController` (single-action `__invoke`), since it's the
only public route with actual side effects (creating an `Inquiry`).

**Inquiry → Klient conversion reuses the Klienten form, not a new one.** `Admin\InquiryController::convert()`
type-hints the *same* `StoreClientRequest` that `Admin\ClientController::store()` uses (same target model, same
validation), and the Anfragen-list view `@include`s the existing `admin.clients._form-fields` partial inside its
own modal, passing an unsaved `new Client(['dog_name' => ..., 'owner_name' => ..., 'email' => ...])` (attributes
from the `Inquiry` prefilled, never persisted) as `$client` — the partial's `$client?->dog_name` accessors work
identically whether `$client` is a real Eloquent-fetched row or one instantiated in-memory for prefill purposes.
`Inquiry` has no `client_id` FK back-reference after conversion (not needed by the plan; the Inquiry's job ends
at `status = Konvertiert`).

**Anfragen has no sidebar nav entry**, consistent with the earlier 5-item-sidebar scope decision — it's reached
via a "Neue Anfragen" section + stat tile on the Dashboard (mirroring the existing "Heutige Termine" section
pattern) linking to `/admin/anfragen`, the same way Kalender's full view is one click from its Dashboard
preview, just without a permanent nav slot.

**Contact-form validation failures redirect to `#kontakt`, not the bare landing route** — `StoreInquiryRequest`
overrides the protected `getRedirectUrl()` (Laravel's default is `url()->previous()`, which drops URL fragments
since browsers never send them to the server) to return `route('landing').'#kontakt'` explicitly, so a rejected
submission re-shows the form in place instead of dumping the visitor back at the page top. `ContactController`
mirrors this on the success path too (`redirect()->to(route('landing').'#kontakt')`) so the flash-message
confirmation is visible without a manual scroll.

**Dropped the hero "testimonial" overlay card, extending the existing Stimmen scope decision** — the mockup's
hero image had a small hardcoded quote-card overlay ("Rocky ist wie ausgewechselt" — Maria W.) separate from
the full Stimmen section already cut. Same underlying problem (fabricated-looking testimonial content with no
admin authoring path), so it's dropped for consistency even though the original scope note only named the nav
item and the dedicated section. The three hero stat numbers (10+ Jahre, 400+ Hunde, 4.9★) stayed, since those
read as ordinary business marketing copy rather than an attributed quote from a specific named client.

**Mobile nav is a bespoke `<dialog>`, not `x-admin.modal`** — reuses the same global backdrop-click-to-close
listener in `app.js` (`event.target instanceof HTMLDialogElement && event.target.open` → `close()`) that the
admin modals already rely on, so no new JS was needed for the hamburger menu. One real gotcha hit while
building it: a `<dialog>` shown via `showModal()` gets `position: fixed; inset: 0` from the browser's UA
stylesheet, which stretches it to the full viewport *height* regardless of Tailwind's `h-auto`/`max-h-none`
utility classes (`inset: 0` sets top **and** bottom to 0, and that wins over `height:auto` since the box is
positioned, not sized, by those offsets) — the panel rendered as a full-screen blank sheet with the links stuck
at the top instead of a tight dropdown. Fixed by setting `bottom: auto` explicitly (inline style, needed
higher specificity than the UA rule) so the dialog's box actually shrinks to its content height while staying
pinned full-width to the top.

**Public nav breakpoint uses Tailwind's `lg:` (1024px), not the mockup's literal 1000px** — same rounding the
admin sidebar already did (860px in the mockup → `md:` 768px in `x-layouts.admin`), applied consistently rather
than introducing a one-off custom breakpoint just for the public nav.

**Fixed an AT/DE inconsistency in the Datenschutz mockup**: `Datenschutz.dc.html` had "10115 Berlin" and a
`.de` email domain while `Impressum.dc.html` (correctly) had "1010 Wien" and `.at` — since the whole app is
built for an Austrian Kleinunternehmerin, the real `legal/datenschutz.blade.php` uses the same Wien/`.at` data
as Impressum instead of copying the mockup's mismatched placeholder.

**404 page**: `resources/views/errors/404.blade.php` is picked up automatically by Laravel's default exception
handler for any `NotFoundHttpException`, independent of `APP_DEBUG` — `Handler::renderHttpException()` checks
`view()->exists('errors.404')` *before* falling back to the Ignition/Whoops debug page, so the custom mockup-styled
404 renders in local dev too, not just in production. 403/500 custom pages are left for M8 (Politur) per the
plan's milestone split.

### Einstellungen (M7): `Setting` is a singleton, every toggle drives something real

`App\Models\Setting::current()` (`firstOrCreate([], [...])`) is the one place that reads/writes the single
settings row — no `Setting::first()` scattered around that would return `null` on a brand-new install. **All
defaults are listed explicitly in `current()`'s create-array, not left to the migration's column defaults** —
hit this the hard way while building it: Eloquent does not reload DB-applied column defaults into the model
instance after `create()`, so a freshly-created row's `notify_appointment_reminders`/`working_days`/etc. were
`null` in memory immediately afterward (even though the DB row itself had the correct default), which silently
made `SendAppointmentReminders` skip every reminder right after the first-ever settings read. The migration
keeps its column defaults too (harmless belt-and-suspenders for any row created outside `current()`), but
`current()`'s explicit values are what actually matters.

**Every notification toggle on the page is wired to a real feature, not a placeholder switch**:
- *Terminerinnerungen* gates the existing M3 `appointments:send-reminders` command (checked at the top of
  `SendAppointmentReminders::handle()`, before it queries any appointments).
- *Neue Anfragen* gates a new `App\Mail\NewInquiryNotification`, sent from `ContactController` right after
  creating the `Inquiry`, only if the toggle is on **and** `Setting.email` is set (no admin email on file ⇒ no
  notification, same "don't send into the void" logic as the M5 dunning email guard).
- *Wöchentliche Zusammenfassung* is a new `reports:weekly-summary` command (`App\Console\Commands\
  SendWeeklySummary`, scheduled Mondays 07:00 in `routes/console.php`) + `App\Mail\WeeklySummaryMail`, a small
  digest (appointments in the next 7 days, inquiries in the last 7 days, open-invoices count/sum) reusing the
  same stat shapes as the Dashboard/Buchhaltung tiles rather than a new reporting service. Guarded the same
  way as the other two.

The mockup's 4th toggle, *Neue Nachrichten* ("E-Mail bei neuer Klientennachricht"), was dropped — it's about
the Nachrichten/chat feature that was cut from scope back in M1, so there's nothing for it to notify about.

**`default_appointment_duration` actually changes the Kalender "+ Neuer Termin" form's preselected duration**
(`AppointmentController::index()` now passes it in; `calendar.blade.php`'s duration `<select>` reads it via
`old('duration_minutes', $defaultAppointmentDuration)` instead of a hardcoded `60`) — a settings field with no
observable effect anywhere else in the app would fail the same "no dead toggles" bar as the notification ones.
**`working_days` is stored and shown as toggle-chips (matching the mockup's Fr/Sa/So-highlighted default) but
is not enforced anywhere** — M3's Kalender doesn't gray out non-working days, and retrofitting that wasn't part
of this milestone's scope; the field exists for future use, not silently ignored data.

**The admin's display name has one source of truth now**: `App\Auth\AdminUserProvider::user()` builds the
`AdminUser` with `name: Setting::current()->name`, and the sidebar/dashboard greeting read the same
`Setting::current()->name` — rename yourself in Einstellungen and the login/dashboard name updates everywhere
immediately, instead of a hardcoded string in the auth layer that Einstellungen couldn't actually change.
Avatar photos upload to the `public` disk (`avatars/…`, same low-sensitivity precedent as M4's expense
receipts — no client data in a profile photo) and render in the sidebar when set, falling back to the plain
color circle otherwise.

### Custom multi-column grid layouts: always `minmax(0, Nfr)` + a stacking breakpoint

Every custom two-column layout in this app (Kalender's month-grid+agenda, Klientenprofil's
Trainingsverlauf+sidebar) uses `grid-cols-1 lg:grid-cols-[minmax(0,Nfr)_minmax(0,1fr)]`, never bare
`grid-template-columns: Nfr 1fr`. Two real bugs found via visual QA, both from skipping this:
- Without `minmax(0, …)`, a track's default min size is its content's min-content width, not its `fr`
  share — one column with any unshrinkable content (a status badge, a long label) silently blows out and
  steals space from the other column, e.g. a `1.1fr 1fr` split rendering as roughly 159px/394px instead of
  proportional. Symptoms look unrelated to grid at first (squished calendar cells, `aspect-square` cells
  rendering as thin slivers) because the *child* shrinks to satisfy the *parent's* wrong track width.
- Without an explicit `grid-cols-1 lg:grid-cols-[...]` breakpoint, the layout never stacks on phone/tablet
  widths — it silently stays two-column and everything gets cramped. Tailwind's `grid-template-columns`
  arbitrary-value syntax needs underscores instead of spaces between tracks:
  `lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]`.

### Auth decision: dropped Auth0, single admin account lives in `.env` (deviates from Description.md)

**2026-07-19, confirmed with the client:** M1 originally built a full Auth0 integration (Universal Login,
`auth0/login` SDK) because `Description.md` mandated it. Revisited later: there is exactly one admin account
(Carmen) and it's always the same person — the operational value Auth0 adds (SSO, MFA, brute-force protection
for a multi-tenant user base) doesn't apply to a single, fixed credential, so it was ripped back out in favor
of plain Laravel session auth. This is a deliberate, confirmed deviation from the client brief's "Auth0"
requirement, not an oversight — noted here the same way the Stimmen/Nachrichten scope cuts are, since
`Description.md` itself is left as the historical brief and not edited after the fact.

**No `users` database table at all** — of the three options considered (DB user + email password-reset,
DB user changeable only via Einstellungen, or credentials fixed in `.env` with no self-service anything), the
client picked the simplest: `ADMIN_EMAIL` + `ADMIN_PASSWORD_HASH` in `.env`, no persistence layer, no
"Passwort vergessen" flow (so `PasswortVergessen.dc.html` stays unbuilt — same call as the original Auth0-era
decision, just for a different reason). Generate/rotate the hash with `php artisan admin:hash-password`, then
paste the output into `.env` by hand; there's no in-app "change password" UI to build in M7 as a result.

Because there's no Eloquent user, `App\Auth\AdminUser` (a plain `Authenticatable`) and
`App\Auth\AdminUserProvider` (a plain `UserProvider`, registered as guard driver `'admin'` in
`AppServiceProvider::boot()`) stand in for the usual `users` table + model. `retrieveByCredentials()` /
`validateCredentials()` compare against `config('admin.email')` / `config('admin.password_hash')` directly —
there's only ever one possible identity, so no query is needed. "Angemeldet bleiben" (remember-me) still works
for real despite there being no DB row to store a `remember_token` in: the token is written to the `cache`
store instead (`Cache::forever(...)`, already database-backed via `CACHE_STORE=database`), which is enough
persistence for a single fixed account without reintroducing a users table just for one column.

`App\Http\Controllers\Auth\LoginController` (`create`/`store`/`destroy`) replaces the old Auth0
login/callback/logout routes; `routes/admin.php`'s middleware is now the framework's plain `auth` (no custom
guard name needed — `config/auth.php`'s `defaults.guard` stays `'web'`, now backed by the `admin` provider).
Logout is a `POST` (CSRF-protected), not the old `GET /logout` — the admin sidebar's "Abmelden" is a small
inline form, matching how other state-changing admin actions in this app already work.

**2026-07-19 bugfix — `sessions.user_id` must be a string, not `foreignId()`:** every authenticated request
(any page load or POST after logging in) was silently failing to persist its session on MySQL, which surfaced
as a `419 Page Expired` on the *next* state-changing request (creating a client, logging out, etc.) — the CSRF
token embedded in whatever page the admin was looking at could never match the server's, because nothing had
actually been saved since login. Took a long debugging session to pin down because it reproduced identically
in fresh incognito windows and even a brand-new Firefox profile (i.e. clearly not a stale-cookie problem), yet
every `curl`-based repro of a plain request cycle worked fine — the failure only appeared once a *real login*
was in the loop. Root cause: `0001_01_01_000000_create_users_table.php`'s `sessions` migration had
`$table->foreignId('user_id')->nullable()->index()` (an `unsigned bigint`, sized for a normal auto-increment
`users.id`), but `App\Auth\AdminUser::getAuthIdentifier()` returns the admin's **email string** (there is no
`users` table, see "Auth decision" above) — inserting that string into a bigint column throws a
`QueryException` under MySQL's strict mode. `Illuminate\Session\DatabaseSessionHandler::performInsert()`
catches that exception and silently falls back to `performUpdate()`, which affects 0 rows since the session
was never actually inserted — so the write silently no-ops instead of erroring loudly. SQLite (what the test
suite runs on) doesn't enforce column types this strictly, so nothing here failed a single Pest test; the
`AdminAccessTest` regression guard only asserts the column's declared type, it can't exercise the actual MySQL
failure. Fixed via `database/migrations/2026_07_19_064819_change_sessions_user_id_to_string.php` (a
driver-gated `ALTER TABLE ... MODIFY user_id VARCHAR(255)`, MySQL-only, cheaper than a `migrate:fresh` that
would've wiped every other table's dev data) plus fixing the original migration to `string('user_id')` so a
fresh install gets it right from the start.

### Politur (M8): A11y-Fixes, Responsive-Fix, Fehlerseiten, Deploy-Probelauf

**Accessibility pass** found and fixed two real gaps, both the same underlying mistake: a custom toggle
switch's `<label>` wrapped the checkbox plus two purely-decorative `<span aria-hidden="true">` elements that
also happened to be the *only* visible text — since `aria-hidden` content is excluded from accessible-name
computation, the checkbox itself ended up with no accessible name at all (a screen reader would announce just
"checkbox, unchecked", no label). Fixed both in `resources/views/admin/settings/index.blade.php` by adding an
explicit `aria-label` to each checkbox: the three Benachrichtigungen toggles (`aria-label="{{ $toggle['label']
}}"`) and the seven Arbeitstage day-chips (`aria-label="{{ $weekdayNames[$day] }}"`, mapping the `Mo`/`Di`/…
abbreviation to a full day name for clarity). Also added `aria-live="polite"` to the Fahrtkosten-Rechner's
live result span (`_fahrtkosten.blade.php`) — its value updates via a plain `oninput` handler with no other
signal to screen reader users that it changed. Everything else audited (modals, forms, icon buttons, images)
was already solid from earlier milestones — all admin modals consistently open via `showModal()` (native
Esc-to-close and focus-trap for free), and every form input already had a real associated `<label>` or an
`sr-only` one.

**Responsive QA** re-confirmed the documented breakpoint deviations still hold (public nav collapses at
Tailwind's `lg:` 1024px, admin sidebar at `md:` 768px) and caught one real overflow bug: the client form's
Adresse/PLZ/Ort row (`admin/clients/_form-fields.blade.php`) used the bare arbitrary-value
`grid-cols-[1fr_120px_1fr]` — exactly the anti-pattern already called out in "Custom multi-column grid
layouts" above, just missed in this one row when the rest of the form was built. On a narrow (mobile-width)
modal, the two `1fr` tracks fell back to their input elements' min-content width (~184px each) instead of
sharing the ~344px available space, blowing the row out to 512px and clipping "Ort" off the edge of the
dialog. Fixed the same way as every other custom grid in this app: `grid-cols-[minmax(0,1fr)_120px_minmax(0,1fr)]`.

**403/500 error pages** (`resources/views/errors/403.blade.php`, `500.blade.php`) extend the existing 404
mockup aesthetic (same logo lockup, oversized serif status code, two-button footer) since the design handoff
never included dedicated mockups for these two. Verified both are actually reachable — `abort(403)`/`abort(500)`
throw an `HttpException`, which Laravel's `renderHttpException()` resolves against `errors.{code}` the same
way it already does for 404, bypassing the `APP_DEBUG`-driven Ignition page regardless of debug mode (see the
404 entry in M6 above) — confirmed via two new tests in `PublicSiteTest.php` that register an ad-hoc route
with `abort()` and assert the custom page renders.

**Cache commands** (`config:cache`/`route:cache`/`view:cache`) were verified to work cleanly — all three
succeed, and the app continues to serve correctly (curl-smoke-tested the landing page, pricing, legal pages,
login, guest-redirect, and a 404) with caches active. Confirmed the expected gotcha as a side effect: running
`config:cache` locally breaks the Pest suite (35 failures) because `phpunit.xml`'s `<env>` overrides (SQLite
`:memory:` DB, etc.) are only read via `env()` at runtime — a cached config file bakes in whatever `.env` said
at cache time instead, so cached config must stay a deploy-only step, never run in local dev. Cleared all
three caches back to normal afterward (confirmed 91 tests green again).

**Netcup deploy dry-run**: since there's no actual Netcup access, did a full isolated rehearsal instead —
copied the working tree (tracked files + this milestone's uncommitted changes) into a scratch directory,
pointed `.env` at a throwaway MySQL database with `APP_ENV=production`/`APP_DEBUG=false`, and ran the real
production pipeline end-to-end: `composer install --no-dev --optimize-autoloader` (confirmed `barryvdh/laravel-dompdf`
is correctly in `require`, not `require-dev`, so nothing breaks), `npm ci && npm run build`, `php artisan
migrate --force` against a genuinely empty database (a real fresh-install test of every migration, including
the M7-era `sessions.user_id` string fix), then `config:cache`/`route:cache`/`view:cache`. This surfaced two
real, previously-undiscovered gaps, both fixed:

- **`config/app.php`'s `'timezone'` key was hardcoded to `'UTC'`, never wired to `env('APP_TIMEZONE')`** —
  `.env`/`.env.example` had `APP_TIMEZONE=Europe/Vienna` sitting there since M0 looking like it configured the
  app's timezone, but silently did nothing (confirmed via `php artisan about` reporting `Timezone: UTC` in the
  dry-run copy despite the env var being set correctly). Concretely, this meant `Schedule::command(
  'reports:weekly-summary')->weeklyOn(1, '07:00')` fired at 07:00 **UTC** — 09:00 or 08:00 Vienna time
  depending on DST — not the 07:00 Vienna-local time the plan and the client both expect for a "Montagmorgen"
  summary. Fixed by changing the key to `env('APP_TIMEZONE', 'UTC')`, matching newer Laravel skeletons; added
  `tests/Feature/AppConfigTest.php` asserting `config('app.timezone') === 'Europe/Vienna'` as a regression
  guard, since nothing else in the suite would have caught a silently-ignored env var.
- **Nothing was ever scheduled to drain the `jobs` table in production.** Every Mailable in this app
  (`PaymentReminderMail`, `AppointmentReminderMail`, `NewInquiryNotification`, `WeeklySummaryMail`) implements
  `ShouldQueue` with `QUEUE_CONNECTION=database`, and locally `start.ps1` keeps a `queue:work` process running
  continuously — but the deploy plan's only documented cron entry was `* * * * * php artisan schedule:run`,
  which drives the three named `Schedule::command(...)` calls and nothing else. On a shared/VPS host with no
  guaranteed persistent process supervisor (Netcup's exact plan is unknown), that means every queued mail would
  sit in the `jobs` table forever and never send — dunning reminders, appointment reminders, new-inquiry
  notifications, the weekly summary, all silently undelivered. Fixed without adding a second cron line: added
  `Schedule::command('queue:work --stop-when-empty --max-time=55 --tries=3')->everyMinute()->withoutOverlapping()`
  to `routes/console.php`, so the one already-planned `schedule:run` cron entry now also drains the queue every
  minute — works identically on a bare shared host or a full VPS, no supervisor/systemd dependency either way.

Also created `deploy.sh` (git pull → `composer install --no-dev --optimize-autoloader` → `npm ci && npm run
build` → `php artisan migrate --force` → `php artisan storage:link` → clear-then-rebuild all three caches),
matching the plan's original outline. `storage:link` is included explicitly because the local dev symlink
(`public/storage`) is an absolute path into this machine's own filesystem — it isn't portable across a git
pull to a different server and must be (re-)created there. The throwaway dry-run database and scratch copy
were deleted after verification; nothing from the rehearsal itself was left behind.

## What this project is

"Mein Hund der Rowdy" — a client-management system ("Klientenverwaltung") for a dog-behavior consultant
(Kleinunternehmerin, Vienna, Austria, § 6 Abs. 1 Z 27 UStG), to be deployed on the client's own Netcup
webserver. It has two halves:

1. A **public marketing site** (landing page, pricing, legal pages, contact form).
2. An **admin backend** ("Praxis-Backend") behind login, for managing appointments, messages, client/dog
   profiles, and bookkeeping (invoices, dunning/Mahnwesen, EA-Rechnung, travel-expense/Fahrtkosten tracking).

## Tech stack (mandated in Description.md — do not substitute another framework/stack)

- **Language**: PHP
- **Framework**: Laravel 12 — Blade for views, Eloquent for the ORM. Follow Laravel's standard conventions and
  project layout; don't deviate into a non-Laravel architecture.
- **Database**: MySQL, database name `mhdr_kv_db`, default port `3306`. Local dev credentials (user/password)
  are recorded in `Description.md` — read them from there rather than duplicating them elsewhere.
- **Frontend extras**: vanilla JavaScript/jQuery/Ajax where needed on top of Blade — no mandated SPA framework.
- **Auth**: ~~Auth0 for login/register (API name "rowdy API", identifier `https://rowdy.api`)~~ — dropped
  post-M1, see "Auth decision" below. Single admin account, plain Laravel session auth, credentials in `.env`.
- **Deployment target**: the client's own Netcup webserver (not a PaaS) — keep deployment assumptions
  compatible with a standard shared/VPS LAMP-style host.

### Architecture approach required by the client

Per `Description.md`: **write the Eloquent models/application code first, then generate the database schema via
Laravel migrations** (code-first, not designing the DB schema by hand first). Keep architecture idiomatic to
Laravel — don't introduce non-standard patterns the framework doesn't call for.

### Non-functional requirements from the client brief (apply to every feature, not just some)

- **Accessibility**: at minimum use `aria-label` attributes; go further where it's sensibly justified.
- **Responsive**: must work on desktop, laptop, tablet, and smartphone.
- **Clean code / best practices**: standard Laravel/PHP conventions.
- **Testing**: test the application properly and fix bugs immediately as they're found, don't defer them.
- **Error handling**: build in proper error handling (not just happy-path code).
- **Build integrity**: the project must always remain buildable — don't leave it in a broken state.
- **Security**: guard against SQL injection and similar (use Eloquent/query bindings, not raw concatenated SQL;
  standard Laravel escaping/validation).
- **Start/stop scripts**: the project folder must contain a script to start the whole app stack (Laravel +
  DB, etc.) and a separate script to stop it. These don't exist yet — create them as part of setting up the
  app, and keep them up to date as the stack evolves.

## Working with the design handoff

Inside the zip, `design_handoff_klientenverwaltung/README.md` is the authoritative spec — read it in full
before implementing any screen. Key points to know going in:

- The `*.dc.html` files are **high-fidelity, throwaway HTML prototypes** from a design tool (inline styles, a
  proprietary `.dc.html` format, no external CSS). They are references for exact look/behavior — **not code to
  copy in**. Rebuild each screen as Blade views/components/partials, translating inline styles to real
  stylesheets and reusable layouts rather than inlining styles Laravel-side.
- `doc-page.js` and `support.js` are prototype-tool runtime helpers and should **not** be ported — `doc-page.js`
  is only useful as a reference for the print layout used by `Rechnung.dc.html` (invoice printing, A4 page).
- Fidelity is meant to be pixel-accurate: colors, type, spacing, and interactions in the prototypes are final,
  not placeholders.
- All prototype forms/tabs are static UI only (no backend, no persistence, client-only state per page) — the
  real app needs an actual data model and persistence layer for clients, dogs, appointments, and invoices, plus
  real email sending for payment reminders, PDF generation for invoices, and file upload for receipts.

### Screens (see README inside the zip for full field-level detail per screen)

Public site: `Landingpage.dc.html`, `Preise.dc.html`, `Impressum.dc.html`, `AGB.dc.html`, `Datenschutz.dc.html`,
`404.dc.html`.
Auth: `Login.dc.html`, `PasswortVergessen.dc.html`.
Admin: `Dashboard.dc.html`, `Kalender.dc.html`, `Nachrichten.dc.html`, `Klienten.dc.html`,
`Klientenprofil.dc.html`, `Buchhaltung.dc.html` (tabs: Ausgangsrechnungen, Eingangsrechnungen, Mahnwesen,
Wiederkehrend, Fahrtkosten, EA-Rechnung, Jahresübersicht), `Rechnung.dc.html` (printable invoice),
`Einstellungen.dc.html`.

Notable cross-screen behavior worth preserving in the real implementation:
- `Preise.dc.html` CTAs link to `Landingpage.dc.html?interesse=<Paketname>#kontakt`; the landing page's contact
  form pre-fills a message from that query param and shows a conditional hint that Erstberatung (initial
  consultation) is required before any package.
- Admin layout is a fixed left sidebar (220px) that collapses to a bottom icon nav under 860px width; the
  public site switches to a hamburger menu under 1000px width.
- The mockup's Nachrichten nav item/badge is dropped per the scope decision above — don't reintroduce it.

### Design tokens (must match exactly when implementing UI)

Colors: background `#f4ede4`, card/panel `#fffaf3`, border `#e7dcc9`, primary/accent `#b5651d` (hover
`#96521a`), secondary green `#6b8e5a`, accent orange `#e0793c`, text dark `#3a2f27`, text muted `#8a7b6a` /
`#6b5a45`. Status colors: success `#e5efdf`/`#4c6b3f`, warning `#fdeee0`/`#a85b1e`, error `#fbe4e0`/`#b3402a`.

Typography: headlines in `Fraunces` (serif, weight 500–700), body/UI in `Nunito Sans` (weight 400/600/700/800),
both via Google Fonts. The brand name renders "der Rowdy" in accent color, italic.

Radii: cards 16–20px, buttons/inputs 10px, chips/badges fully rounded (20px). No shadow system — flat cards
with a 1px border only.

Icons are inline SVG, outline style, 2–2.4px stroke — no icon library in the prototypes.

### Legal/domain constraints to respect

The consultant operates under Austria's Kleinunternehmerregelung (§ 6 Abs. 1 Z 27 UStG), so invoices must
carry the small-business VAT-exemption notice, not charge VAT. Travel expense (Fahrtkosten) reimbursement uses
the Austrian per-km rate (`0,50 €/km`). Impressum content follows Austrian requirements (§ 5 ECG, § 25
Mediengesetz). Dunning (Mahnwesen) follows Austrian Mahnstufen/Verzugszinsen conventions. Keep these
jurisdiction-specific details intact when implementing bookkeeping/legal screens rather than genericizing them.
