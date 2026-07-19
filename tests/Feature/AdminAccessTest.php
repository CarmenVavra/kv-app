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

it('stores sessions.user_id as a string, not a numeric foreign key', function () {
    // Regression guard: AdminUser::getAuthIdentifier() returns the admin's email, not a numeric
    // users.id (there is no users table). A bigint user_id column silently breaks every session
    // write once logged in - MySQL throws on the non-numeric insert, DatabaseSessionHandler
    // swallows it and falls back to a no-op update, so the session (and CSRF token) never
    // persists past login. SQLite's lax typing means this only actually breaks on MySQL, so this
    // test can only guard the schema declaration, not exercise the failure itself.
    expect(Illuminate\Support\Facades\Schema::getColumnType('sessions', 'user_id'))->toBe('varchar');
});
