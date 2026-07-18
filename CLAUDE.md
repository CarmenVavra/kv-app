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
public reply mechanism. Contact-form submissions become `Inquiry` records that Lena answers by ordinary email
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
snapshots `rate_per_km` at creation time, so a later change to `config('accounting.kilometergeld_pro_km')`
never rewrites historical entries.

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

### Auth0 setup gotcha — `registerMiddleware` must stay `false`

`config/auth0.php` sets `'registerMiddleware' => false` and `defaults.guard` in `config/auth.php` stays
`'web'` (a plain, never-logged-in Eloquent guard), on purpose — do not "simplify" this back to the package
defaults. If `registerMiddleware` is `true`, `auth0/login` force-appends `AuthenticatorMiddleware` to the
*entire* `web` middleware group, which calls `auth()->shouldUse('auth0-session')` on every request. Combined
with `SESSION_DRIVER=database`, Laravel's `DatabaseSessionHandler` then calls `auth()->guard()->id()` on
*every* request to store it alongside the session row — which eagerly constructs the Auth0 SDK client and
throws `` `domain` must be configured `` even for the public marketing pages, not just `/admin`. Only
`routes/admin.php` requests the Auth0 guard, explicitly, via `auth:auth0-session` middleware — that's what
correctly scopes the Auth0 dependency to the admin backend only. `app/Auth0/UserRepository.php` overrides the
package's generic user wrapper to find-or-create the local `App\Models\User` by `auth0_id` and reject any
login whose email doesn't match `config('admin.email')` (`ADMIN_EMAIL` env var).

**Real Auth0 tenant credentials are still needed** (`AUTH0_DOMAIN`, `AUTH0_CLIENT_ID`, `AUTH0_CLIENT_SECRET`,
`ADMIN_EMAIL` in `.env`) before `/admin/*` can actually be used or visually QA'd — until then it correctly
500s with "`domain` must be configured" rather than silently misbehaving. Tests don't need real credentials:
`phpunit.xml` sets syntactically-valid fake Auth0 env values so the guard can construct in the test
environment without ever calling out to Auth0.

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
- **Auth**: Auth0 for login/register (API name "rowdy API", identifier `https://rowdy.api`).
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
