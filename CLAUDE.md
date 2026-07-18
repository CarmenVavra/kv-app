# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Current state of this repository

This repository does **not yet contain an application codebase**. It currently holds only planning/reference
material:

- `Description.md` — the project brief from the client (in German). Read this in full before planning or
  scaffolding anything; it is the source of truth for stack, constraints, and deployment target.
- `UI_MOCKUPS/meinHundDerRowdy.zip` and `UI_MOCKUPS/meinHundDerRowdy_02.zip` — two versions of the same design
  handoff (the second adds a `screenshots/` folder with PNG renders of each screen). Unzip either one to get
  `design_handoff_klientenverwaltung/`.

There is no `composer.json`, artisan console, migrations, or test suite yet. The first real task in this repo
is to **plan the project** (explicitly requested in `Description.md`) before scaffolding the Laravel app. Once
scaffolded, update this file with the actual `composer`/`artisan` commands, test invocation, etc.

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
- Nachrichten (messages) badge count on the sidebar nav should reflect unread messages app-wide.

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
