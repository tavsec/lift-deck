<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class FilamentAuthenticate extends Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return; /** @phpstan-ignore-line */
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentOrDefaultPanel();

        $canAccess = $user instanceof FilamentUser
            ? $user->canAccessPanel($panel)
            : config('app.env') === 'local';

        if (! $canAccess) {
            $this->redirectUnauthorized($request, $user);
        }
    }

    protected function redirectUnauthorized(Request $request, mixed $user): never
    {
        if (method_exists($user, 'isCoach') && $user->isCoach()) {
            throw new HttpResponseException(redirect()->route('coach.dashboard'));
        }

        if (method_exists($user, 'isClient') && $user->isClient()) {
            throw new HttpResponseException(redirect()->route('client.dashboard'));
        }

        throw new HttpResponseException(redirect()->route('login'));
    }
}
