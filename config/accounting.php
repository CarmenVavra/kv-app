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

    // Amtliches Kilometergeld Oesterreich.
    'kilometergeld_pro_km' => 0.50,

    // Grobe Ruecklage-Schaetzung fuer die Einkommensteuer (keine Steuerberatung).
    'steuerruecklage_quote' => 0.20,
];
