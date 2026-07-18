# Handoff: Klientenverwaltung „Mein Hund der Rowdy"

## Overview
Verwaltungssystem für eine Hundeverhaltensberaterin (Wien, Kleinunternehmerin gem. § 6 Abs. 1 Z 27 UStG): öffentliche Website mit Preisen/Kontakt, Admin-Login und ein Praxis-Backend für Termine, Nachrichten, Klienten-/Hunde-Profile und Buchhaltung (Rechnungen, Mahnwesen, EA-Rechnung, Fahrtkosten).

## About the Design Files
Die Dateien in diesem Paket sind **Design-Referenzen in HTML** — Prototypen, die Aussehen und Verhalten zeigen, kein Produktionscode zum direkten Übernehmen. Aufgabe: die Designs in der Ziel-Codebase (React, Vue, o.ä. — bzw. bei fehlendem Setup das am besten geeignete Framework) mit deren etablierten Mustern/Bibliotheken nachbauen.

## Fidelity
**High-fidelity**: Farben, Typografie, Abstände und Interaktionen sind final gemeint. Pixelgenau nachbauen, aber mit den Libraries/Patterns der Zielumgebung.

## Screens / Views

### 1. Landingpage.dc.html — Öffentliche Website
- Sticky-freier Header mit Logo, Nav (Leistungen, Preise, Über mich, Stimmen, Termin anfragen, Admin-Login), Hamburger-Menü unter 1000px Breite
- Hero: Badge + H1 (`clamp(30px,5vw,52px)`) + Subline + 2 CTAs + 3 Stat-Kacheln + Bildkachel (4:5) mit Testimonial-Card overlay
- Leistungen-Sektion: 4 Karten mit SVG-Icon, Titel, Beschreibung, Hintergrund `#f4ede4`
- Über-mich-Sektion: Bild + Text + Credential-Chips
- Stimmen-Sektion: 3 Testimonial-Karten mit Sternen
- Kontakt-CTA: farbiger Block (`#b5651d`) mit Formular (Name, E-Mail, Hundename, Nachricht — Nachricht wird per `?interesse=`-Query-Parameter aus Preise.dc.html vorausgefüllt, dazu ein Hinweistext bei Paketen dass Erstberatung Voraussetzung ist)
- Footer: Copyright, Impressum-/Datenschutz-Links
- Anchor-Links (#leistungen, #kontakt etc.) werden per `scrollIntoView` in `componentDidMount` behandelt (nötig wegen Query-Param-Navigation)

### 2. Preise.dc.html — Preise & Pakete
- Gleicher Header/Nav wie Landingpage
- 3 Preiskarten: Erstberatung (90€, Pflicht-Erststep, Voraussetzung für alle Pakete), Trainingspaket 4er (320€, hervorgehoben/highlight, dunkler Hintergrund `#b5651d`), Trainingspaket 8er (580€)
- Jede Karte: Titel, Preis, Einheit, Beschreibung, Feature-Liste mit Checkmark-SVGs, CTA-Button
- CTA-Buttons verlinken zu `Landingpage.dc.html?interesse=<Paketname>#kontakt`
- "Gut zu wissen"-Box mit Hinweisen (Hausbesuche/Kilometergeld 0,50€/km AT, Stornobedingungen, Gültigkeit)

### 3. Login.dc.html — Admin-Login
- Zentrierte Card, Logo, E-Mail/Passwort-Felder, "Angemeldet bleiben"-Checkbox, "Passwort vergessen"-Link, Anmelden-Button → Dashboard.dc.html

### 4. PasswortVergessen.dc.html
- Zweistufiger Flow: Formular (E-Mail) → Bestätigungs-Screen ("E-Mail unterwegs") mit Zurücksetzen-Möglichkeit

### 5. Dashboard.dc.html — Admin-Übersicht
- Linke Sidebar (220px, fixiert): Logo, Nav (Dashboard, Kalender, Nachrichten, Klienten, Buchhaltung, Einstellungen — Nachrichten-Badge zeigt ungelesene Anzahl), Beraterin-Kärtchen unten
- Unter 860px: Sidebar ausgeblendet, stattdessen Bottom-Nav (fixed, alle Menüpunkte als Icons)
- Header: Begrüßung, Datum + Terminanzahl, Suche, "+ Neuer Termin"
- 4 Statistik-Kacheln (Termine heute, neue Anfragen, aktive Klienten, ungelesene Nachrichten)
- Zweispaltiges Layout: heutige Termine (Zeit, Hund, Klient, Typ, Status) + Nachrichten-Vorschau (Avatar, ungelesen-Punkt, Preview-Text)
- Klienten-&-Hunde-Grid (Karten mit Avatar, Rasse, Halter, Tags)

### 6. Kalender.dc.html
- Gleiche Sidebar/Nav wie Dashboard
- Monatsraster (7 Spalten, `minmax(0,1fr)` Tracks, `aspect-ratio:1`-Zellen, heutiger Tag hervorgehoben, Punkt-Indikator bei Terminen)
- Tagesagenda rechts (Terminliste mit Zeit/Dauer/Hund/Klient/Typ)

### 7. Nachrichten.dc.html
- Sidebar + zweispaltiges Chat-Layout: Konversationsliste (Suche, Avatar, ungelesen-Badge, Fett-Gewicht bei ungelesen) + aktive Konversation (Header mit Klient/Hund-Info + Link zu Klientenprofil, Chat-Bubbles links/rechts, Eingabefeld unten)

### 8. Klienten.dc.html
- Sidebar + Header mit Suche + "+ Neuer Klient" (öffnet Modal mit Formular: Hundename, Rasse, Alter, Halter, E-Mail, Telefon, Verhaltensthema-Select, Notizen)
- Filter-Chip-Reihe (Alle, Leinenaggression, Angst, Welpen, Impulskontrolle)
- Karten-Grid, jede Karte verlinkt zu Klientenprofil.dc.html

### 9. Klientenprofil.dc.html — Detailseite
- Profil-Header (Avatar, Name, Rasse/Alter/Halter, Tags, "Nachricht senden"/"Termin buchen"/"Bearbeiten"/"Löschen")
- Bearbeiten öffnet vorausgefülltes Formular-Modal; Löschen öffnet Bestätigungsdialog (führt zu Klienten.dc.html)
- Trainingsverlauf-Timeline (Datum, farbiger Punkt, Titel, Notiz)
- Seitenspalte: Kontakt-Infos, nächster Termin, editierbare Notizen (Textarea)

### 10. Buchhaltung.dc.html
- Header mit Kleinunternehmer-Hinweis (§ 6 Abs. 1 Z 27 UStG AT), Export-CSV-Button, kontextabhängiger "+"-Button (nur bei Rechnungs-Tabs sichtbar)
- 4 Stat-Kacheln (Einnahmen/Ausgaben/Gewinn/offene Rechnungen)
- Tab-Leiste (flex-wrap): Ausgangsrechnungen, Eingangsrechnungen, Mahnwesen, Wiederkehrend, Fahrtkosten, EA-Rechnung, Jahresübersicht
- **Ausgangsrechnungen**: Tabelle (Datum, Klient/Leistung, Betrag, Status-Badge, PDF-Link → öffnet Rechnungs-Vorschau-Modal → Link zu Rechnung.dc.html zum Drucken)
- **Eingangsrechnungen**: Tabelle (Datum, Lieferant/Kategorie, Betrag, Status, Beleg-Link); "+ Beleg erfassen" öffnet anderes Modal (Lieferant, Kategorie-Select, Datum, Betrag, Datei-Upload-Platzhalter)
- **Mahnwesen**: Hinweistext zu österreichischen Mahnstufen/Verzugszinsen; Tabelle überfälliger Rechnungen mit Tage-überfällig-Badge und Aktions-Button ("Zahlungserinnerung senden" / "2. Mahnung senden") → öffnet E-Mail-Entwurf-Modal (Betreff + Body vorausgefüllt, editierbar)
- **Wiederkehrend**: Tabelle mit Vorlagen (Titel, Klient, Betrag, Rhythmus, nächstes Datum, Aktiv/Pausiert-Badge), "+ Neue Vorlage"
- **Fahrtkosten**: Kilometer-Eingabe → Live-Berechnung (0,50 €/km, AT-Kilometergeld), "Als Ausgabe erfassen"
- **EA-Rechnung**: Zeilenliste (Einnahmen, Ausgaben, Gewinn, USt-Hinweis, Steuerrücklage-Schätzung)
- **Jahresübersicht**: Quartalstabelle (Einnahmen/Ausgaben/Gewinn je Quartal) + Rücklage-Hinweis

### 11. Rechnung.dc.html — Druckbare Rechnung
- Nutzt `doc-page.js` Web Component (A4, 1.4in Rand) für echtes Print-Layout
- Rechnungskopf (Absender/Empfänger), Leistungspositionen-Tabelle, Summenblock, Kleinunternehmer-Hinweis, Bankverbindung, Dank-Zeile
- "← Zurück zur Buchhaltung"-Link fixiert oben links

### 12. Einstellungen.dc.html
- Profil (Avatar, Name/E-Mail/Telefon/Praxisname-Felder)
- Benachrichtigungen (4 Toggle-Switches, custom gebaut aus Checkbox+2 absolut positionierten Spans)
- Kalender & Verfügbarkeit (Termindauer-Select, Arbeitstage Fr–So als Toggle-Chips)
- Abbrechen/Speichern-Buttons

### 13. Impressum.dc.html / AGB.dc.html / Datenschutz.dc.html
- Einheitliches Rechtsseiten-Layout: Header mit Logo+Zurück-Link, Content max-width 760px, nummerierte/betitelte Abschnitte, Footer mit Cross-Links
- Impressum: österreichische Pflichtangaben (§ 5 ECG, § 25 Mediengesetz, WKO-Mitgliedschaft, Kleinunternehmerregelung)

### 14. 404.dc.html
- Zentriert: Logo, große "404", Titel "Diese Fährte verliert sich…", Text, 2 CTAs (Dashboard/Website)

## Interactions & Behavior
- Modals: `position:fixed; inset:0` Overlay mit `rgba(58,47,39,0.35)`-Backdrop, Klick auf Overlay schließt, Klick auf Card-Inhalt stoppt Propagation
- Alle Formulare sind aktuell **statisch** (kein echtes Backend/Persistenz) — Submit-Buttons sind reine UI-Platzhalter außer Navigation
- Tabs in Buchhaltung: reiner Client-State (kein Routing)
- Query-Parameter-Weitergabe: `Preise.dc.html` → `Landingpage.dc.html?interesse=X#kontakt`, dort vorausgefüllte Nachricht + bedingter Hinweistext
- Responsive: Sidebar-Layouts (Admin) wechseln unter 860px zu Bottom-Nav; Landingpage/Preise wechseln unter 1000px zu Hamburger-Menü
- Hover-States: durchgängig leichte Farbvertiefung (z.B. `#b5651d`→`#96521a`, `#fff`→`#f4ede4`)

## State Management
Pro Seite lokaler State (kein globaler Store im Prototyp):
- Buchhaltung: `tab`, `showModal`, `modalType`, `previewInvoice`, `reminderTarget`, `kmRate`
- Klienten/Klientenprofil: `showModal`/`showEdit`/`showDelete`
- Landingpage/Preise: `menuOpen`, `interesse` (aus URL)
- Login/PasswortVergessen: `step`

Für die Produktivversion nötig: Auth-Session, Klienten-/Hunde-/Termin-/Rechnungs-Datenmodell (siehe Felder in den Screens), echter Mail-Versand für Mahnungen, PDF-Generierung für Rechnungen, Datei-Upload für Belege.

## Design Tokens
**Farben**
- Hintergrund: `#f4ede4` (warm beige)
- Card/Panel: `#fffaf3`
- Border: `#e7dcc9`
- Primär/Akzent: `#b5651d` (Hover `#96521a`)
- Sekundär Grün: `#6b8e5a`
- Akzent Orange: `#e0793c`
- Text dunkel: `#3a2f27`
- Text gedämpft: `#8a7b6a` / `#6b5a45`
- Status: Erfolg `#e5efdf`/`#4c6b3f`, Warnung `#fdeee0`/`#a85b1e`, Fehler `#fbe4e0`/`#b3402a`

**Typografie**
- Headlines: `'Fraunces', serif`, Weight 500–700
- Fließtext/UI: `'Nunito Sans', sans-serif`, Weight 400/600/700/800
- Markenname-Stil: "der Rowdy" in Akzentfarbe, kursiv

**Radius/Sonstiges**
- Cards: 16–20px Radius
- Buttons/Inputs: 10px Radius
- Chips/Badges: 20px (voll gerundet)
- Kein Schatten-System — flache Cards mit 1px Border

## Assets
- Google Fonts: Fraunces (500/600/700), Nunito Sans (400/600/700/800) via `fonts.googleapis.com`
- Fotos: Unsplash-Platzhalter (Hero: `photo-1601758228041-f3b2795255f1`, Über mich: `photo-1516734212186-a967f81ad0d7`) — vor Launch durch echte Fotos der Beraterin/Hunde ersetzen
- Icons: inline SVG (Outline-Stil, 2–2.4px Stroke), keine Icon-Bibliothek
- `doc-page.js`: Web-Component-Starter für Print-Layout (Rechnung.dc.html)

## Files
Alle `.dc.html`-Dateien in diesem Ordner sind eigenständige, im Browser lauffähige HTML-Prototypen (Design-Component-Format der Entwurfsumgebung — inline Styles, keine externen CSS-Dateien). `doc-page.js` und `support.js` sind Runtime-Hilfsdateien des Prototyp-Tools und müssen in der Zielumgebung NICHT übernommen werden (nur als Referenz für das Rechnung.dc.html-Printlayout).
