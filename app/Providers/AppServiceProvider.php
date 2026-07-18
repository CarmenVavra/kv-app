<?php

namespace App\Providers;

use App\Auth0\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ueberschreibt Auth0\Laravel\ServiceProviderAbstract::register(), das an dieser Stelle
        // bereits gelaufen ist (alle register()-Methoden laufen vor jeder boot()-Methode) -
        // ersetzt den generischen Auth0-User-Wrapper durch unser eigenes User-Model.
        $this->app->singleton('auth0.repository', fn () => new UserRepository());
    }
}
