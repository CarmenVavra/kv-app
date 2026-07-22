<?php

return [

    /*
     * Gesetzlicher Verzugszinssatz gegenueber Verbrauchern (B2C, AT): 4 % p.a. (§ 1000 Abs. 1 ABGB),
     * verrechenbar ab der 2. Mahnung. Die hoeheren 9,2 % ueber Basiszinssatz (§ 456 UGB) und der
     * pauschale Betreibungsschaden von 40 € (§ 1333 Abs. 2 ABGB) gelten nur im B2B-Verkehr zwischen
     * Unternehmern und sind hier bewusst NICHT anwendbar - die Kundschaft besteht aus Privatpersonen.
     */
    'verzugszinssatz' => (float) env('VERZUGSZINSSATZ', 0.04),

    // Grobe Ruecklage-Schaetzung fuer die Einkommensteuer (keine Steuerberatung).
    'steuerruecklage_quote' => 0.20,

    /*
     * Ab wie vielen Verzugstagen welche Mahnstufe fuer eine offene Rechnung empfohlen wird -
     * steuert nur die Anzeige der "Mahnstufe"-Spalte in der Ausgangsrechnungsliste (Richtwerte
     * nach oesterreichischer Praxis). Die tatsaechliche Mahnung wird weiterhin manuell im
     * Mahnwesen-Tab ausgeloest, dies ist reine Entscheidungshilfe.
     *   - Zahlungserinnerung: ca. 3-5 Tage nach Verzug (hoeflich, ohne Gebuehren)
     *   - 1. Mahnung:         ca. 10-14 Tage nach Verzug (bestimmter, noch ohne Zinsen)
     *   - 2. Mahnung:         ca. 20-25 Tage nach Verzug (4 % Verzugszinsen, Androhung Inkasso/Gericht)
     */
    'mahnstufe_ab_tag' => [
        'erinnerung' => (int) env('MAHNSTUFE_ERINNERUNG_AB_TAG', 3),
        'mahnung_1' => (int) env('MAHNSTUFE_MAHNUNG_1_AB_TAG', 10),
        'mahnung_2' => (int) env('MAHNSTUFE_MAHNUNG_2_AB_TAG', 20),
    ],
];
