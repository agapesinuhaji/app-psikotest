<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class FilamentAuthenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {

            $panel = Filament::getCurrentPanel();

            if ($panel) {
                return $panel->getLoginUrl();
            }

            return route('login');
        }

        return null;
    }
}
