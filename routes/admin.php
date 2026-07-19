<?php

use App\Http\Controllers\Admin\Accounting\DunningController;
use App\Http\Controllers\Admin\Accounting\ExpenseController;
use App\Http\Controllers\Admin\Accounting\InvoiceController;
use App\Http\Controllers\Admin\Accounting\InvoicePdfController;
use App\Http\Controllers\Admin\Accounting\MileageController;
use App\Http\Controllers\Admin\Accounting\RecurringInvoiceController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kalender', [AppointmentController::class, 'index'])->name('calendar');
    Route::post('/kalender', [AppointmentController::class, 'store'])->name('appointments.store');

    Route::prefix('anfragen')->name('inquiries.')->group(function (): void {
        Route::get('/', [InquiryController::class, 'index'])->name('index');
        Route::patch('/{inquiry}/kontaktiert', [InquiryController::class, 'markContacted'])->name('mark-contacted');
        Route::post('/{inquiry}/konvertieren', [InquiryController::class, 'convert'])->name('convert');
        Route::patch('/{inquiry}/archivieren', [InquiryController::class, 'archive'])->name('archive');
    });

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
        Route::get('/rechnungen/{invoice}/pdf', [InvoicePdfController::class, 'download'])->name('invoices.pdf');

        Route::post('/ausgaben', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::patch('/ausgaben/{expense}/bezahlt', [ExpenseController::class, 'markPaid'])->name('expenses.mark-paid');

        Route::post('/mahnwesen/{invoice}/senden', [DunningController::class, 'send'])->name('dunning.send');

        Route::post('/wiederkehrend', [RecurringInvoiceController::class, 'store'])->name('recurring.store');
        Route::patch('/wiederkehrend/{recurringInvoiceTemplate}/umschalten', [RecurringInvoiceController::class, 'toggleActive'])->name('recurring.toggle-active');

        Route::post('/fahrtkosten', [MileageController::class, 'store'])->name('mileage.store');
    });

    Route::view('/einstellungen', 'admin.settings.index')->name('settings.index');
});
