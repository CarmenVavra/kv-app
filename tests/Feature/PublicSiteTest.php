<?php

it('renders the landing page without the dropped Stimmen section', function () {
    $this->get(route('landing'))
        ->assertOk()
        ->assertSee('Ein entspannter Hund beginnt mit Verständnis.')
        ->assertSee('Leinentraining')
        ->assertSee('Hallo, ich bin Carmen.')
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
        ->assertSee('Erstberatung')
        ->assertSee('90 €')
        ->assertSee('Trainingspaket 4er')
        ->assertSee('320 €')
        ->assertSee('Trainingspaket 8er')
        ->assertSee('580 €');
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
