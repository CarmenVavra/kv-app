<?php

namespace App\Http\Requests\Admin;

// Gleiche Regeln wie beim Anlegen (inkl. status_option) - der Status ist jetzt bereits im
// StoreAppointmentRequest enthalten, daher keine eigene rules()-Ueberschreibung mehr noetig.
class UpdateAppointmentRequest extends StoreAppointmentRequest
{
}
