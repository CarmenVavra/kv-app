<?php

return [

    /*
     * Gesetzliche Verzugszinsen im B2B-Verkehr (AT, § 456 UGB): Basiszinssatz + 9,2 Prozentpunkte
     * p.a., verrechenbar ab der 2. Mahnung. Der Basiszinssatz schwankt und muesste bei Bedarf
     * aktualisiert werden (z.B. via .env) - hier als Naeherungswert hinterlegt.
     */
    'basiszinssatz' => (float) env('BASISZINSSATZ', 0.0388),
    'verzugszinssatz_aufschlag' => 0.092,

    // Pauschaler Betreibungsschaden, ab der 2. Mahnung (AT, § 1333 Abs. 2 ABGB).
    'betreibungsschaden' => 40.00,

    // Grobe Ruecklage-Schaetzung fuer die Einkommensteuer (keine Steuerberatung).
    'steuerruecklage_quote' => 0.20,

    /*
     * Ab wie vielen Verzugstagen welche Mahnstufe fuer eine offene Rechnung empfohlen wird -
     * steuert nur die Anzeige der "Mahnstufe"-Spalte in der Ausgangsrechnungsliste (Richtwerte
     * nach oesterreichischer Praxis). Die tatsaechliche Mahnung wird weiterhin manuell im
     * Mahnwesen-Tab ausgeloest, dies ist reine Entscheidungshilfe.
     *   - Zahlungserinnerung: ca. 3-5 Tage nach Verzug (hoeflich, ohne Gebuehren)
     *   - 1. Mahnung:         ca. 10-14 Tage nach Verzug (bestimmter, Spesen/Zinsen moeglich)
     *   - 2. Mahnung:         ca. 20-25 Tage nach Verzug (Androhung Inkasso/Gericht)
     */
    'mahnstufe_ab_tag' => [
        'erinnerung' => (int) env('MAHNSTUFE_ERINNERUNG_AB_TAG', 3),
        'mahnung_1' => (int) env('MAHNSTUFE_MAHNUNG_1_AB_TAG', 10),
        'mahnung_2' => (int) env('MAHNSTUFE_MAHNUNG_2_AB_TAG', 20),
    ],
];
