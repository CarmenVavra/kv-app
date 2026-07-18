<?php

use App\Http\Controllers\Admin\Accounting\ExpenseController;
use App\Http\Controllers\Admin\Accounting\InvoiceController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:auth0-session'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kalender', [AppointmentController::class, 'index'])->name('calendar');
    Route::post('/kalender', [AppointmentController::class, 'store'])->name('appointments.store');

    Route::prefix('klienten')->name('clients.')->group(function (): void {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::patch('/{client}/notizen', [ClientController::class, 'updateNotes'])->name('update-notes');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('buchhaltung')->name('accounting.')->group(function (): void {
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        Route::get('/export', [AccountingController::class, 'export'])->name('export');

        Route::post('/rechnungen', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::patch('/rechnungen/{invoice}/bezahlt', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('/rechnungen/{invoice}/drucken', [InvoiceController::class, 'print'])->name('invoices.print');

        Route::post('/ausgaben', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::patch('/ausgaben/{expense}/bezahlt', [ExpenseController::class, 'markPaid'])->name('expenses.mark-paid');
    });

    Route::view('/einstellungen', 'admin.settings.index')->name('settings.index');
});
