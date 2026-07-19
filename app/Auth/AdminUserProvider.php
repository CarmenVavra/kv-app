<?php

namespace App\Auth;

use App\Models\Setting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Liefert immer denselben, aus der .env gebauten Admin-Account - es gibt keine users-Tabelle.
 * Der Remember-Token liegt im Cache (nicht in der .env, die ist global und nicht pro Login), damit
 * "Angemeldet bleiben" echt funktioniert statt nur eine wirkungslose Checkbox zu sein.
 */
class AdminUserProvider implements UserProvider
{
    private const REMEMBER_TOKEN_CACHE_KEY = 'admin.remember_token';

    private function user(): AdminUser
    {
        return new AdminUser(
            email: (string) config('admin.email'),
            name: Setting::current()->name,
            passwordHash: (string) config('admin.password_hash'),
        );
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        $user = $this->user();

        return strcasecmp((string) $identifier, $user->getAuthIdentifier()) === 0 ? $user : null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        if (Cache::get(self::REMEMBER_TOKEN_CACHE_KEY) !== $token) {
            return null;
        }

        return $this->retrieveById($identifier);
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        Cache::forever(self::REMEMBER_TOKEN_CACHE_KEY, $token);
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $user = $this->user();

        return isset($credentials['email']) && strcasecmp($credentials['email'], $user->getAuthIdentifier()) === 0
            ? $user
            : null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (! $user->getAuthPassword()) {
            return false;
        }

        return Hash::check($credentials['password'] ?? '', $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // No-op: der Hash liegt in der .env, es gibt keinen Datensatz, den wir umschreiben koennten.
    }
}
