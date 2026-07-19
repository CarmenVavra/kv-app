<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin-Zugangsdaten
    |--------------------------------------------------------------------------
    |
    | Diese App hat genau einen Admin-Account (die Hundeverhaltensberaterin), verwaltet direkt
    | ueber die .env - keine users-Tabelle. Passwort-Hash erzeugen: `php artisan admin:hash-password`.
    |
    */

    'email' => env('ADMIN_EMAIL'),
    'password_hash' => env('ADMIN_PASSWORD_HASH'),

];
