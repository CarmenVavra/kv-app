<?php

it('renders the landing page without the dropped Stimmen section', function () {
    $this->get(route('landing'))
        ->assertOk()
        ->assertSee('Ein entspannter Hund beginnt mit Verständnis.')
        ->assertSee('Leinenaggression')
        ->assertSee('Vom Albtraum zum Vertrauen')
        ->assertDontSee('Stimmen')
        ->assertDontSee('Was Klient');
});

it('prefills the contact message and shows a package hint from the interesse query param', function () {
    $this->get(route('landing', ['interesse' => 'Trainingspaket 4er']))
        ->assertOk()
        ->assertSee('Ich interessiere mich für: Trainingspaket 4er')
        ->assertSee('Voraussetzung für alle Trainingspakete');
});

it('does not show the package hint for the Erstberatung interesse', function () {
    $this->get(route('landing', ['interesse' => 'Erstberatung']))
        ->assertOk()
        ->assertDontSee('Voraussetzung für alle Trainingspakete ist, klären wir dort auch dein Interesse');
});

it('renders the pricing page with all three packages', function () {
    $this->get(route('pricing'))
        ->assertOk()
        ->assertSee('Erstgespräch &amp; Analyse', false)
        ->assertSee('110 €')
        ->assertSee('Paket „Sicher durch den Alltag“', false)
        ->assertSee('310 €')
        ->assertSee('Die Einzelstunde')
        ->assertSee('75 €');
});

it('renders the legal pages', function () {
    $this->get(route('legal.impressum'))->assertOk()->assertSee('§ 5 ECG');
    $this->get(route('legal.agb'))->assertOk()->assertSee('Geltungsbereich');
    $this->get(route('legal.datenschutz'))->assertOk()->assertSee('Verantwortlicher');
});

it('renders a custom 404 page for unknown routes', function () {
    $this->get('/diese-seite-gibt-es-nicht')
        ->assertNotFound()
        ->assertSee('Diese Fährte verliert sich');
});

it('renders a custom 403 page for forbidden requests', function () {
    \Illuminate\Support\Facades\Route::get('/test-403', fn () => abort(403));

    $this->get('/test-403')
        ->assertForbidden()
        ->assertSee('Hier ist Sitz und Bleib gefragt');
});

it('renders a custom 500 page for server errors', function () {
    \Illuminate\Support\Facades\Route::get('/test-500', fn () => abort(500));

    $this->get('/test-500')
        ->assertServerError()
        ->assertSee('Da ist etwas schiefgelaufen');
});
