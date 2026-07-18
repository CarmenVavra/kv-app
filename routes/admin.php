<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:auth0-session'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('/kalender', 'admin.calendar')->name('calendar');
    Route::view('/klienten', 'admin.clients.index')->name('clients.index');
    Route::view('/buchhaltung', 'admin.accounting.index')->name('accounting.index');
    Route::view('/einstellungen', 'admin.settings.index')->name('settings.index');
});
