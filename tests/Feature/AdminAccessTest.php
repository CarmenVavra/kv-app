<?php

it('redirects guests to the login page', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

it('renders the login page for guests', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Mit Auth0 anmelden');
});
