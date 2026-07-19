<?php

it('redirects guests to the login page', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

it('renders the login page for guests', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Anmelden');
});

it('logs the admin in with the correct credentials and reaches the dashboard', function () {
    $this->post(route('login.store'), [
        'email' => config('admin.email'),
        'password' => 'test-password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated();
});

it('rejects an incorrect password', function () {
    $this->post(route('login.store'), [
        'email' => config('admin.email'),
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs the admin out', function () {
    $this->post(route('login.store'), [
        'email' => config('admin.email'),
        'password' => 'test-password',
    ]);
    $this->assertAuthenticated();

    $this->post(route('logout'))->assertRedirect(route('landing'));

    $this->assertGuest();
});
