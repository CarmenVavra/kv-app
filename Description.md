Ich möchte mit dir eine klientenverwaltung für Verhaltensberater für Hunde erstellen (Österreichisches Gesetz).
Diese Applikation möchte ich dann auf meinen Netcup Webserver stellen.
Ich habe mit Claude-Design UI-Mockups erstellt, nach denen die Applikation erstellt werden soll.
Folgende Punkte sind dabei zu beachten: 

- barrierefrei .. zumindest aria-label verwenden, mehr darf auch gemacht werden, wenn sinnvoll
- halte dich an clean-code
- arbeite nach best-practice
- responsive .. für Desktop, Laptop, Tablet und Smartphone
- teste die Applikation ordentlich und behebe Fehler sofort
- baue ein error-handling ein
- build .. das Projekt muss sich immer builden lassen
- architecture best-practice .. halte dich bei der Architectur an das gegebene Framework. In unserem Fall Laravel und erst Code dann mittels migration die Datenbank.
- beachte security-themen SQL-Injection oder Ähnliches

- erstelle im Projektordner ein Script zum Starten der App (Laravel, DB, ...)
- erstelle im Projektordner ein Script zum Beenden der App (Laravel, DB, ...)

Name des Unternehmens: "Mein Hund der Rowdy"
Projektordner: C:\Users\carme\Desktop\PROJEKTE\MHDR_KV_APP
Programmiersprache: PHP
Framework: Laravel 12 (Blade, Eloquent)
sonstiges: wenn nötig - Javascript, JQuery, Ajax, etc.
Datenbank: MySql
Datenbankname: mhdr_kv_db
Port: 3306
Benutzer: root
Pw: 1234
Auth0 Login/Register: 
- API-Name: rowdy API
- Identifier: https://rowdy.api

Die UI-Mockups findest du unter C:\Users\carme\Desktop\PROJEKTE\MHDR_KV_APP\UI_MOCKUPS\meinHundDerRowdy.zip


PROJEKTPLAN - CLAUDE-CODE:
# Mein Hund der Rowdy — Klientenverwaltung: Projektplan

## Context

Das Repository enthält aktuell nur den Projektauftrag (`Description.md`) und die Design-Mockups
(`UI_MOCKUPS/`) — noch keine Laravel-Anwendung. Der Auftrag verlangt ausdrücklich, das Projekt zuerst zu
planen, bevor Code entsteht. Dieser Plan ist das Ergebnis: er liest den Auftrag, die 14 Mockup-Screens
(`design_handoff_klientenverwaltung/*.dc.html` + README) und die bereits auf diesem Rechner vorhandene
Dev-Umgebung (XAMPP mit PHP 8.2.12, Composer 2.7.2, Node 22) zusammen und leitet daraus eine konkrete
Laravel-12-Architektur, ein Datenmodell und eine Build-Reihenfolge ab.

**Wichtige Scope-Entscheidung (mit Nutzerin abgestimmt):** Der "Nachrichten"-Bereich aus dem Mockup
(Chat-UI mit Konversationsliste) **entfällt vollständig** — kein Zwei-Wege-Chat, kein Klienten-Login, kein
öffentlicher Antwort-Link. Kontaktanfragen von der Landingpage werden als `Inquiry`-Datensätze erfasst und
von Lena ganz normal per E-Mail außerhalb der App beantwortet. Das vereinfacht die Architektur spürbar (kein
Conversation/Message-Modell, keine neue unauthentifizierte öffentliche Fläche mit eigenem
Rate-Limiting-Bedarf) und reduziert den Sidebar-Nav auf: Dashboard, Kalender, Klienten, Buchhaltung,
Einstellungen (5 statt 6 Einträge). Der "Nachricht senden"-Button im Klientenprofil bleibt visuell erhalten,
verlinkt aber nur als `mailto:`-Link auf die E-Mail-Adresse der/des Klientin/en.

Alle anderen Screens/Funktionen aus dem Mockup werden 1:1 in der Zielarchitektur abgebildet (Design gilt laut
Handoff-README als pixelgenau final, nicht als Platzhalter).

---

## Zwei grundlegende Modellierungs-Entscheidungen

- **Client ist ein einziges, bewusst nicht normalisiertes Model — ein Datensatz pro Hund, mit den
  Halter-Feldern direkt darin** (kein separates `Dog`-Model, kein `Client hasMany Dog`). Fachlich
  bestätigt: ein Trainingsplan ist immer auf genau einen Hund zugeschnitten, jeder Hund hat seinen eigenen,
  komplett unabhängigen Trainingsverlauf. Hat ein Halter mehrere Hunde, wird er entsprechend mehrfach
  angelegt — einmal pro Hund, mit den Halter-Kontaktdaten jedes Mal mit erfasst. Das entspricht genau dem
  Mockup-Formular ("Neuen Klienten erfassen": Hundename, Rasse, Alter, Halter/in, E-Mail, Telefon,
  Verhaltensthema, Notizen — alles ein Formular, ein Datensatz).
- **Appointment (Kalender-Termin) und "Trainingsverlauf-Eintrag" sind dieselbe Tabelle.** Beide nutzen
  identische Typen (Erstberatung/Folgetermin/Hausbesuch/Video-Check-in), und das "Nächster
  Termin"-Feld im Klientenprofil ist sichtbar nur die nächste zukünftige Zeile derselben Daten, die der
  Kalender zeigt. Ein `Appointment`-Model mit Status-Enum und `notes`-Feld bedient beide UI-Ansichten, und
  hängt direkt an `Client` (also am jeweiligen Hund), nicht an einem übergeordneten Halter-Model.
- **Invoice (Ausgangsrechnung) und Expense (Eingangsrechnung) bleiben getrennte Models**, kein gemeinsames
  Model mit `direction`-Flag — ihre Felder divergieren zu stark (Rechnungsnummer/Line-Items/Mahnwesen vs.
  freier Lieferant-Text/Kategorie/Beleg-Upload), ein gemeinsames Model würde nur zu bedingter Verzweigung
  ohne Vorteil führen.

---

## Tech-Stack & lokale Dev-Umgebung

- Laravel 12 (Blade, Eloquent), PHP 8.2+ (vorhanden: 8.2.12 via XAMPP), MySQL (`mhdr_kv_db`, Port 3306,
  lokal root/1234 aus `Description.md`)
- Composer 2.7.2 und Node v22/npm 11 sind bereits installiert — kein zusätzliches Setup nötig
- **Kein Docker/Sail** — das Deployment-Ziel ist klassisches Shared-/VPS-LAMP-Hosting bei Netcup ohne
  Container; lokal wird nur XAMPPs MySQL verwendet (`mysql_start.bat`/`mysql_stop.bat`), die App selbst läuft
  über `php artisan serve` (keine Apache-vhost-Konfiguration nötig für den Alltag)
- Tailwind CSS via Vite-Plugin, Design-Tokens (Farben, Radien, Typografie) als Theme-Konfiguration abgebildet
  — kein SPA-Framework, wie im Auftrag gefordert
- Google Fonts (Fraunces, Nunito Sans) werden selbst gehostet (`@fontsource/*` via Vite) statt von
  `fonts.googleapis.com` geladen — vermeidet den bekannten DSGVO-Kritikpunkt (IP-Übertragung an Google) bei
  einer österreichischen Kanzlei-/Beratungs-Website, kostet nichts an visueller Treue
- Testing: **Pest** (Laravel-12-Standard), da für die vielen kleinen Domänen-Logik-Szenarien (Mahnwesen,
  EÜR-Berechnung) besser lesbar als reines PHPUnit
- PDF: `barryvdh/laravel-dompdf` — reine PHP-Lösung ohne Headless-Chrome/Node-Abhängigkeit, läuft auf
  Netcup-Shared-Hosting ohne Zusatzbinaries
- Auth: `auth0/login` (offizielles Laravel-Paket), Universal-Login-Flow (siehe unten)

---

## Verzeichnisstruktur (Laravel-12-Skeleton, kein `Kernel.php` mehr)

```
routes/
  web.php        # öffentliche Website + Auth0 login/callback/logout + Rechtsseiten
  admin.php      # alles hinter auth0-Middleware, prefix('admin')->name('admin.')
  console.php    # Schedule::command(...) Definitionen

app/Http/Controllers/
  Public/  {LandingController, PricingController, LegalController, ContactController, NotFoundController}
  Auth/    Auth0Controller.php   # login()/callback()/logout()
  Admin/
    DashboardController.php
    AppointmentController.php   # Kalender + Trainingsverlauf (ein Model)
    ClientController.php        # ein Datensatz pro Hund inkl. Halter-Feldern
    InquiryController.php       # Anfragen-Liste + Konvertierung zu Client
    Accounting/
      InvoiceController.php, ExpenseController.php, DunningController.php,
      RecurringInvoiceController.php, MileageController.php, ReportController.php,
      InvoicePdfController.php
    SettingsController.php

app/Models/            # siehe Datenmodell unten
app/Mail/              # PaymentReminderMail, AppointmentReminderMail, NewInquiryNotification, WeeklySummaryMail
app/Console/Commands/  # GenerateRecurringInvoices, SendAppointmentReminders, SendWeeklySummary
app/Services/          # DunningService, MileageCalculator, InvoiceNumberGenerator, EuerCalculator
app/Auth0/UserRepository.php   # Find-or-create lokaler User anhand Auth0-Sub, harte Ablehnung fremder E-Mails

resources/views/
  layouts/  public.blade.php, admin.blade.php (Sidebar 220px + Bottom-Nav <860px), print.blade.php
  components/public/*, components/admin/{sidebar-nav-item,stat-tile,status-badge,filter-chip,modal}.blade.php
  public/{landing,pricing,404}.blade.php
  legal/{impressum,agb,datenschutz}.blade.php
  admin/dashboard.blade.php
  admin/appointments/{calendar,_day-agenda}.blade.php
  admin/clients/{index,show,_form-modal}.blade.php
  admin/accounting/{index,_outgoing,_incoming,_dunning,_recurring,_mileage,_euer,_yearly}.blade.php
  admin/accounting/invoices/{print,pdf}.blade.php
  admin/settings/index.blade.php
```

**Buchhaltung-Tabs sind echte serverseitige Routen** (`GET /admin/buchhaltung?tab=mahnwesen`), kein reiner
Client-State wie im Prototyp — funktioniert ohne JS, ist bookmarkbar und einfach zu testen. Modals (Neuer
Klient, Bearbeiten, Löschen, Beleg erfassen, Mahnwesen-E-Mail-Entwurf, Rechnungsvorschau) werden inline
gerendert und per kleinem Vanilla-JS-Helfer umgeschaltet — bei deaktiviertem JS bleibt eine echte Fallback-Seite
erreichbar (progressive enhancement). Echtes Ajax/jQuery wird nur für die Fahrtkosten-Live-Berechnung
gebraucht (triviale Client-Arithmetik).

---

## Datenmodell

| Model | Kernfelder | Beziehungen |
|---|---|---|
| `User` | `auth0_id`, `name`, `email`, `avatar_path` | — (eine Zeile: Lena) |
| `Client` | Hund: `dog_name`, `breed`, `age_text`, `behavior_topic` (Enum: Leinenaggression/Stress/Mantrailing/Impulskontrolle/Welpentraining/Sonstiges), `dog_notes`, `color` (Avatar-Farbe). Halter/in: `owner_name`, `email`, `phone`, `address_line`, `postal_code`, `city`. `status` (aktiv/archiviert) | `hasMany(Appointment)`, `hasMany(Invoice)` — **ein Datensatz pro Hund**; derselbe Halter mit zwei Hunden ergibt zwei `Client`-Zeilen mit doppelt erfassten Halter-Feldern (siehe Modellierungs-Entscheidung oben) |
| `Appointment` | `client_id`, `type` (Erstberatung/Folgetermin/Hausbesuch/Video-Check-in), `starts_at`, `duration_minutes`, `status` (ausstehend/bestätigt/abgeschlossen/abgesagt), `notes`, `reminder_sent_at` | `belongsTo(Client)` — Trainingsverlauf ist damit automatisch pro Hund isoliert |
| `Inquiry` | `name`, `email`, `dog_name`, `message`, `interested_package` (nullable), `status` (neu/kontaktiert/konvertiert/archiviert) | Konvertierung → `Client` (neuer Datensatz für den betreffenden Hund) |
| `Invoice` | `number` (R-0042-Format, unique), `client_id`, `appointment_id` (nullable), `issue_date`, `due_date`, `paid_at` (nullable), `pdf_path` | `belongsTo(Client)`, `hasMany(InvoiceLineItem)`, `hasMany(PaymentReminder)` |
| `InvoiceLineItem` | `invoice_id`, `description`, `quantity`, `unit_price`, `amount` | `belongsTo(Invoice)` |
| `PaymentReminder` | `invoice_id`, `level` (erinnerung/mahnung_1/mahnung_2), `subject`, `body` (tatsächlich versendeter Text), `interest_amount`, `collection_fee`, `sent_at` | `belongsTo(Invoice)` |
| `Expense` | `date`, `supplier`, `category` (Bürobedarf/Trainingsmaterial/Weiterbildung/Fahrzeugkosten/Telefon & Internet/Sonstiges), `amount`, `status`, `receipt_path` (nullable) | — |
| `RecurringInvoiceTemplate` | `title`, `client_id`, `description`, `amount`, `frequency` (monatlich/wöchentlich), `next_run_date`, `active` | `belongsTo(Client)` |
| `MileageEntry` | `date`, `km`, `rate_per_km` (Snapshot, 0,50 zum Erfassungszeitpunkt), `amount`, `expense_id` (nullable) | optional `belongsTo(Expense)` |
| `Setting` | Singleton: `name`, `email`, `phone`, `praxisname`, `avatar_path`, `notify_new_inquiries`, `notify_appointment_reminders`, `notify_weekly_summary`, `default_appointment_duration`, `working_days` (JSON) | — (`Setting::current()`) |

Anmerkungen:
- `Invoice.status` (Offen/Bezahlt/Überfällig) ist ein **berechneter Accessor** aus `paid_at`/`due_date`, keine
  Spalte, die ein Job täglich umschaltet — verhindert eine zweite Wahrheitsquelle.
- Filter-Chips in Klienten (Alle/Leinenaggression/…) sind `Client.behavior_topic`, kein eigenes Tag-Model.
  "6 Sitzungen" im Klientenprofil ist `$client->appointments()->count()`, kein gespeichertes Feld.
- `Client`-Löschung nutzt Laravel SoftDeletes im Hintergrund, obwohl der Bestätigungsdialog "endgültig" sagt —
  günstiges Sicherheitsnetz gegen Fehlklicks, ohne eigene Restore-UI.

## Migrationsreihenfolge (Code-first laut Auftrag)

Pro Modul: Model + Migration + Factory zusammen anlegen (`php artisan make:model X -mf`), Model-Felder/
Relationen + einen Pest-Test zuerst schreiben, dann Migration minimal ausfüllen, migrieren, Tests grün,
committen. Reihenfolge:

1. `User`, `Setting` 2. `Client` (+ Factory, da alle späteren Tests ihn brauchen) 3. `Appointment`
4. `Invoice`, `InvoiceLineItem` 5. `Expense` 6. `RecurringInvoiceTemplate` 7. `MileageEntry`
8. `PaymentReminder` 9. `Inquiry`

## Auth0-Integration

`auth0/login` (Composer), **Universal-Login-Flow (Redirect)**, nicht eingebettetes Formular — für einen
einzigen Admin-Account überwiegt der Sicherheitsgewinn (MFA, Brute-Force-Schutz, kein Passwortfeld im
eigenen Code) die visuelle Kontrolle. `.env`: `AUTH0_DOMAIN`, `AUTH0_CLIENT_ID`, `AUTH0_CLIENT_SECRET`,
`AUTH0_AUDIENCE=https://rowdy.api`. `/login` zeigt eine Blade-Seite im Mockup-Look mit einem
"Mit Auth0 anmelden"-Button, der zu Auth0 weiterleitet; Branding (Logo, `#b5651d`, Fraunces/Nunito Sans) wird
im Auth0-Dashboard auf die gehostete Login-Seite übertragen. `PasswortVergessen.dc.html` entfällt in der
Produktivversion — Auth0 verwaltet den Passwort-Reset nativ. `Auth0UserRepository` legt beim ersten Login
lokal einen `User` an und lehnt jede Auth0-E-Mail außer der vorab hinterlegten Admin-Adresse hart ab (403).

*Hinweis:* Falls `composer require auth0/login` gegen `laravel/framework ^12` beim Scaffolding nicht auflöst,
neueste getaggte Version fixieren statt `--ignore-platform-reqs`; Fallback wäre das rohe `auth0/auth0-php`-SDK
mit einem dünnen eigenen Guard.

## PDF-Generierung (Rechnung.dc.html)

`barryvdh/laravel-dompdf`. Dompdf unterstützt kein CSS-Grid und nur teilweise Flexbox — daher **zwei
getrennte Blade-Views**, die dieselbe `Invoice::toPrintData()`-Datenaufbereitung nutzen (keine doppelte
Summen-/Kleinunternehmer-Text-Logik):
- `admin/accounting/invoices/print.blade.php` — modernes CSS, `@media print`, für den "Drucken"-Button
- `admin/accounting/invoices/pdf.blade.php` — Table/Inline-Block-Layout (dompdf-sicher), für echte PDF-Dateien

Jede finalisierte `Invoice` erzeugt beim Anlegen eine echte PDF-Datei (`storage/app/invoices/{number}.pdf`)
für Archivierung/Mahnwesen-Anhänge, nicht nur On-the-fly-Druck.

## E-Mail

Mailables: `PaymentReminderMail` (sendet exakt den im Modal editierten Betreff/Text, `PaymentReminder`
speichert diesen Text vor dem Versand für die Nachvollziehbarkeit), `AppointmentReminderMail` (1h vorher, an
`Setting.notify_appointment_reminders` gekoppelt), `NewInquiryNotification` (an Lena, an
`Setting.notify_new_inquiries` gekoppelt), `WeeklySummaryMail` (montags). Lokal `MAIL_MAILER=log` (kein
Setup nötig), produktiv SMTP über Netcups Postfach.

## Geplante Jobs (`routes/console.php`, kein `Kernel.php` in Laravel 12)

- `invoices:generate-recurring` — täglich, erzeugt fällige `Invoice`+`InvoiceLineItem` aus aktiven
  `RecurringInvoiceTemplate`-Zeilen, rückt `next_run_date` vor (idempotent)
- `appointments:send-reminders` — alle 15 Min., Termine in der nächsten Stunde ohne `reminder_sent_at`
- `reports:weekly-summary` — montags 07:00 Uhr

Lokal: `php artisan schedule:work` (läuft im Start-Skript mit). Netcup-Produktion: ein Cron-Eintrag
(`* * * * * php artisan schedule:run`), Laravel fächert intern an alle drei Jobs auf.

## Teststrategie (Pest)

Unit: `MileageCalculator`, `DunningService::nextAction()`, `EuerCalculator`, Rechnungssummen über
`InvoiceLineItem`-Collections. Feature: volle HTTP-Zyklen mit `RefreshDatabase`, CRUD-Flows,
Mahnwesen-Versand-und-Persistenz, `Artisan::call()` für Scheduled Commands. Konkrete Szenarien für die
heikelsten Bereiche:

- **Mahnwesen:** richtige Aktion je nach Vorstufe (Erinnerung → 1. Mahnung → 2. Mahnung), Zinsen/Gebühr
  (9,2 % Basiszinssatz + 40 €) nur ab der 2. Mahnung, nie bei der kostenlosen Erinnerung; gespeicherter Text
  entspricht exakt dem editierten Text, nicht der Vorlage
- **Kleinunternehmer-Rechnung:** USt. ist immer 0,00 €, egal wie viele Positionen; § 6 Abs. 1 Z 27
  UStG-Hinweis erscheint immer auf der generierten PDF
- **Fahrtkosten:** exakt `km * 0,50`, Rate wird auf dem `MileageEntry` eingefroren (spätere Ratenänderung
  ändert historische Einträge nicht)
- **Wiederkehrende Rechnungen:** korrekte nächste Rechnungsnummer, korrektes Vorrücken von `next_run_date`
  je nach Rhythmus, keine Generierung bei pausierter Vorlage, Idempotenz bei doppeltem Lauf am selben Tag
- **Fehlerfälle (nicht nur Happy Path):** abgelaufener/ungültiger Auth0-Callback, PDF-Fehler darf einen
  Mahnungs-Versand nicht stillschweigend verschlucken, Mail-Fehler darf den `PaymentReminder`-Datensatz nicht
  verlieren, Beleg-Upload lehnt falsche Mime-Types/zu große Dateien ab

## Start-/Stop-Skripte

PowerShell-Skripte im Projektroot (`.bat`-Wrapper für Doppelklick):

- **start.ps1**: startet XAMPP-MySQL (`mysql_start.bat`), dann `php artisan serve`, `php artisan queue:work`,
  `php artisan schedule:work` als Hintergrundprozesse; PIDs werden in `.run/pids.json` gespeichert
- **stop.ps1**: beendet alle in `pids.json` gespeicherten Prozesse, stoppt danach XAMPP-MySQL

Auf Netcup selbst braucht es kein Start/Stop-Skript (Always-on-Hosting) — stattdessen ein `deploy.sh`
(`git pull && composer install --no-dev --optimize-autoloader && php artisan migrate --force && ... cache`)
und ein Apache-vhost auf `/public` + der oben genannte Cron-Eintrag.

## Build-Reihenfolge / Meilensteine

Jeder Meilenstein endet mit grünem `php artisan test` + manuellem Smoke-Test, bevor der nächste beginnt —
das ist die konkrete, prüfbare Bedeutung von "Projekt muss immer buildbar bleiben".

1. **M0 — Scaffold:** `laravel new` mit Pest, `.env` für `mhdr_kv_db`, Tailwind+Design-Tokens, selbst gehostete
   Fonts, `start.ps1`/`stop.ps1`, leere Layouts rendern, `/up` grün
2. **M1 — Auth0 + Shell:** Login/Callback/Logout, Admin-Sidebar/Bottom-Nav (5 Einträge), Middleware-Schutz,
   Test für Redirect bei fehlendem Login
3. **M2 — Client-Kern:** Migration+Factory für das zusammengeführte `Client`-Model (Hund+Halter),
   Klienten-Liste + Filter-Chips + Neu/Bearbeiten/Löschen, Klientenprofil (inkl. `mailto:`-Link statt Chat)
4. **M3 — Termine:** Kalender-Monatsraster + Tagesagenda, Dashboard-Kachel "Termine heute",
   Klientenprofil-Trainingsverlauf/Nächster-Termin (selbes `Appointment`-Model, `belongsTo(Client)`,
   damit pro Hund vollständig getrennt), Erinnerungs-Job
5. **M4 — Buchhaltung Kern:** Invoice+LineItems+Expense CRUD, Ausgangs-/Eingangsrechnungen-Tabs,
   Statistik-Kacheln, CSV-Export (`fputcsv`, keine Zusatzabhängigkeit)
6. **M5 — Buchhaltung erweitert:** Mahnwesen-Zustandsmaschine + E-Mail-Versand, Wiederkehrend +
   Scheduled-Job, Fahrtkosten-Rechner + verknüpfte Ausgabe, EÜR-/Jahresübersicht, dompdf-Rechnungserzeugung
7. **M6 — Öffentliche Website:** Landingpage, Preise, Impressum/AGB/Datenschutz (§ 5 ECG/§ 25 Mediengesetz),
   404, Kontaktformular → `Inquiry`, Dashboard-Kachel "Neue Anfragen", Anfrage→Klient-Konvertierung
8. **M7 — Einstellungen:** Profil/Benachrichtigungen/Kalender-Einstellungen, verdrahtet mit den Toggles aus
   M3/M5
9. **M8 — Politur:** Barrierefreiheit (aria-labels, Fokus-States, per Tastatur bedienbare Modals),
   Responsive-QA an allen vier Breakpoints (860px/1000px-Umbrüche aus dem Mockup), Fehlerseiten (403/404/500),
   `config:cache`/`route:cache`/`view:cache`, Netcup-Deploy-Probelauf

## Verifikation

Nach jedem Meilenstein: `php artisan test` (Pest) muss grün sein; danach manueller Smoke-Test im Browser
über `php artisan serve` (Login → betroffene Screens → Formulare mit gültigen und ungültigen Daten). Vor
Abschluss des Gesamtprojekts (M8): kompletter Durchlauf aller Kernflüsse (Klient anlegen → Termin buchen →
Rechnung erzeugen → als überfällig markieren → Mahnung senden → PDF prüfen) plus Responsive-Check auf
Desktop/Tablet/Smartphone-Breiten.

## Referenzierte Quelldateien

- `Description.md` — Auftrag/Stack-Vorgaben
- `UI_MOCKUPS/meinHundDerRowdy_02/design_handoff_klientenverwaltung/README.md` — Screen-Übersicht,
  Design-Tokens
- `.../Buchhaltung.dc.html`, `.../Klientenprofil.dc.html`, `.../Rechnung.dc.html`, `.../Kalender.dc.html`,
  `.../Klienten.dc.html`, `.../Dashboard.dc.html`, `.../Einstellungen.dc.html` — feldgenaue UI-Referenz für
  das Datenmodell