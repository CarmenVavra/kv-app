<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | Diese App hat genau einen Admin-Account (die Hundeverhaltensberaterin).
    | Jeder Auth0-Login mit einer abweichenden E-Mail-Adresse wird abgelehnt.
    |
    */

    'email' => env('ADMIN_EMAIL'),

];
