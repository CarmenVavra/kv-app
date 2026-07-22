<?php

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Enums\InvoiceStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Nur die Authenticate-Middleware wird ausgeschaltet (nicht die ganze "web"-Gruppe) - sonst
// fehlen Session/CSRF/$errors, die die Views brauchen. Der "Gast wird zu /login umgeleitet"-Fall
// ist bereits in AdminAccessTest abgedeckt.

it('shows the outgoing tab by default with stat tiles', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky', 'owner_name' => 'Maria Weber']);
    $invoice = Invoice::factory()->for($client)->paid()->create();
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 80]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index'))
        ->assertOk()
        ->assertSee('Ausgangsrechnungen')
        ->assertSee('Rocky');
});

it('switches to the incoming tab via the tab query parameter', function () {
    Expense::factory()->create(['supplier' => 'BüroPlus']);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'in']))
        ->assertOk()
        ->assertSee('BüroPlus');
});

it('creates an outgoing invoice from an open appointment and marks it billed', function () {
    Storage::fake('local');
    $client = Client::factory()->create();
    $appointment = Appointment::factory()->for($client)->create([
        'type' => AppointmentType::Erstberatung,
        'status' => AppointmentStatus::Abgeschlossen,
    ]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'appointments' => [$appointment->id],
            'appointment_amounts' => [$appointment->id => '90.00'],
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    $invoice = Invoice::sole();
    $lineItem = $invoice->lineItems()->sole();

    expect($invoice->number)->toBe('R-0001')
        ->and($invoice->client_id)->toBe($client->id)
        ->and((float) $invoice->total_amount)->toBe(90.0)
        ->and($invoice->status)->toBe(InvoiceStatus::Offen)
        ->and($lineItem->appointment_id)->toBe($appointment->id)
        ->and($lineItem->description)->toContain('Erstberatung');

    // Der Termin gilt jetzt als verrechnet und darf nicht mehr als offene Leistung erscheinen.
    expect(Appointment::unbilled()->whereKey($appointment->id)->exists())->toBeFalse();
});

it('generates sequential invoice numbers', function () {
    Storage::fake('local');
    $client = Client::factory()->create();
    Invoice::factory()->for($client)->create(['number' => 'R-0007']);
    $appointment = Appointment::factory()->for($client)->create(['status' => AppointmentStatus::Abgeschlossen]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'appointments' => [$appointment->id],
            'appointment_amounts' => [$appointment->id => '60.00'],
        ]);

    expect(Invoice::where('number', 'R-0008')->exists())->toBeTrue();
});

it('rejects creating an invoice without a client or any leistung', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [])
        ->assertSessionHasErrors(['client_id', 'appointments']);

    expect(Invoice::count())->toBe(0);
});

it('only bills open appointments belonging to the selected client', function () {
    Storage::fake('local');
    $client = Client::factory()->create();
    $otherClient = Client::factory()->create();
    $foreignAppointment = Appointment::factory()->for($otherClient)->create(['status' => AppointmentStatus::Abgeschlossen]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'appointments' => [$foreignAppointment->id],
            'appointment_amounts' => [$foreignAppointment->id => '60.00'],
            'sonstiges' => '1',
            'sonstiges_description' => 'Telefonberatung',
            'sonstiges_amount' => '20.00',
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    $invoice = Invoice::sole();
    expect($invoice->lineItems)->toHaveCount(1)
        ->and((float) $invoice->total_amount)->toBe(20.0);
});

it('adds a Sonstiges line item with a free description and amount', function () {
    Storage::fake('local');
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'sonstiges' => '1',
            'sonstiges_description' => 'Telefonberatung',
            'sonstiges_amount' => '25.00',
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    $invoice = Invoice::sole();
    $lineItem = $invoice->lineItems()->sole();

    expect($lineItem->description)->toBe('Telefonberatung')
        ->and($lineItem->appointment_id)->toBeNull()
        ->and((float) $lineItem->amount)->toBe(25.0);
});

it('requires a description and amount when Sonstiges is checked', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'sonstiges' => '1',
        ])
        ->assertSessionHasErrors(['sonstiges_description', 'sonstiges_amount']);

    expect(Invoice::count())->toBe(0);
});

it('adds a mileage line item as km times the configured rate', function () {
    Storage::fake('local');
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'charge_mileage' => '1',
            'mileage_km' => '24',
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    $invoice = Invoice::sole();
    $mileageItem = $invoice->lineItems()->where('description', 'like', 'Fahrtkosten%')->sole();

    expect($mileageItem->description)->toContain('24,0 km')
        ->and((float) $mileageItem->amount)->toBe(12.0)
        ->and((float) $invoice->total_amount)->toBe(12.0);
});

it('adds a Kurzparkschein line item as hours times two half-hour blocks times the rate', function () {
    Storage::fake('local');
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'charge_parking' => '1',
            'parking_hours' => '2',
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    $invoice = Invoice::sole();
    $parkingItem = $invoice->lineItems()->where('description', 'like', 'Kurzparkschein%')->sole();

    // 2 Stunden = 4 Halbstunden-Bloecke x 1,20 EUR Standard-Tarif = 4,80 EUR.
    expect((float) $parkingItem->amount)->toBe(4.8)
        ->and((float) $invoice->total_amount)->toBe(4.8);
});

it('requires the km when charging mileage and the hours when charging parking', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'charge_mileage' => '1',
            'charge_parking' => '1',
        ])
        ->assertSessionHasErrors(['mileage_km', 'parking_hours']);

    expect(Invoice::count())->toBe(0);
});

it('marks an invoice as paid', function () {
    $invoice = Invoice::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.accounting.invoices.mark-paid', $invoice))
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'out']));

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::Bezahlt);
});

it('marks an unpaid invoice past its due date as overdue', function () {
    $invoice = Invoice::factory()->overdue()->create();

    expect($invoice->status)->toBe(InvoiceStatus::Ueberfaellig);
});

it('prints an invoice', function () {
    $invoice = Invoice::factory()->create();
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 80]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.invoices.print', $invoice))
        ->assertOk()
        ->assertSee($invoice->number)
        ->assertSee('§ 6 Abs. 1 Z 27 UStG');
});

it('creates an expense with an uploaded receipt', function () {
    Storage::fake('public');

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.expenses.store'), [
            'supplier' => 'BüroPlus',
            'category' => ExpenseCategory::Buerobedarf->value,
            'date' => today()->toDateString(),
            'amount' => '32.90',
            'receipt' => UploadedFile::fake()->create('beleg.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'in']));

    $expense = Expense::sole();
    expect($expense->supplier)->toBe('BüroPlus')
        ->and($expense->status)->toBe(ExpenseStatus::Offen)
        ->and($expense->receipt_path)->not->toBeNull();

    Storage::disk('public')->assertExists($expense->receipt_path);
});

it('rejects an expense receipt with a disallowed file type', function () {
    Storage::fake('public');

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.expenses.store'), [
            'supplier' => 'BüroPlus',
            'category' => ExpenseCategory::Buerobedarf->value,
            'date' => today()->toDateString(),
            'amount' => '32.90',
            'receipt' => UploadedFile::fake()->create('beleg.exe', 100, 'application/x-msdownload'),
        ])
        ->assertSessionHasErrors(['receipt']);

    expect(Expense::count())->toBe(0);
});

it('marks an expense as paid', function () {
    $expense = Expense::factory()->create(['status' => ExpenseStatus::Offen]);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.accounting.expenses.mark-paid', $expense))
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'in']));

    expect($expense->fresh()->status)->toBe(ExpenseStatus::Bezahlt);
});

it('exports outgoing invoices as csv', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky']);
    $invoice = Invoice::factory()->for($client)->create(['number' => 'R-0001']);
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 80]);

    $response = $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.export', ['tab' => 'out']))
        ->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('text/csv')
        ->and($response->streamedContent())->toContain('R-0001')->toContain('Rocky');
});

it('exports incoming expenses as csv', function () {
    Expense::factory()->create(['supplier' => 'BüroPlus']);

    $response = $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.export', ['tab' => 'in']))
        ->assertOk();

    expect($response->streamedContent())->toContain('BüroPlus');
});

it('only counts paid invoices from the current month as income', function () {
    $paidThisMonth = Invoice::factory()->create([
        'issue_date' => today(),
        'due_date' => today()->addDays(14),
        'paid_at' => today(),
    ]);
    $paidThisMonth->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 100]);

    $unpaid = Invoice::factory()->create([
        'issue_date' => today(),
        'due_date' => today()->addDays(14),
        'paid_at' => null,
    ]);
    $unpaid->lineItems()->create(['description' => 'Folgetermin', 'quantity' => 1, 'unit_price' => 50]);

    $response = $this->withoutMiddleware(Authenticate::class)->get(route('admin.accounting.index'));

    expect($response->viewData('stats')['income'])->toBe(100.0)
        ->and($response->viewData('stats')['openCount'])->toBe(1);
});
