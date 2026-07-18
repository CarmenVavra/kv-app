<?php

namespace App\Auth0;

use App\Models\User;
use Auth0\Laravel\UserRepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserRepository implements UserRepositoryContract
{
    public function fromAccessToken(array $user): ?Authenticatable
    {
        return $this->findOrCreate($user);
    }

    public function fromSession(array $user): ?Authenticatable
    {
        return $this->findOrCreate($user);
    }

    /**
     * @param array<string, mixed> $auth0User
     */
    private function findOrCreate(array $auth0User): Authenticatable
    {
        $email = $auth0User['email'] ?? null;
        $adminEmail = config('admin.email');

        if (! $adminEmail || ! $email || strcasecmp($email, $adminEmail) !== 0) {
            throw new HttpException(403, 'Dieser Account ist nicht fuer den Admin-Zugang freigeschaltet.');
        }

        return User::query()->updateOrCreate(
            ['auth0_id' => $auth0User['sub']],
            [
                'name' => $auth0User['name'] ?? $email,
                'email' => $email,
                'avatar_path' => $auth0User['picture'] ?? null,
            ],
        );
    }
}
