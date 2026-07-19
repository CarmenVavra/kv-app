<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Es gibt genau einen Admin-Account (Carmen) - Zugangsdaten liegen in der .env, nicht in einer
 * Datenbanktabelle. Diese Klasse macht daraus trotzdem ein normales Authenticatable, damit
 * auth()->user(), die "auth"/"guest"-Middleware und Auth::attempt() unveraendert funktionieren.
 */
class AdminUser implements Authenticatable
{
    private string $rememberToken = '';

    public function __construct(
        public readonly string $email,
        public readonly string $name,
        private readonly string $passwordHash,
    ) {}

    public function getAuthIdentifierName(): string
    {
        return 'email';
    }

    public function getAuthIdentifier(): string
    {
        return $this->email;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return $this->passwordHash;
    }

    public function getRememberToken(): string
    {
        return $this->rememberToken;
    }

    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
