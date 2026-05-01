<?php

namespace App\Providers;

use App\Listeners\EnsureProfessionalMeteredItem;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Nightwatch\Facades\Nightwatch;

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
        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            $user = $request->user();

            if ($user->isAdmin()) {
                return route('filament.admin.pages.dashboard');
            }

            if ($user->isClient()) {
                return route('client.dashboard');
            }

            return route('coach.dashboard');
        });

        Event::listen(function (CommandStarting $event) {
            if (in_array($event->command, [
                'octane:status',
            ])) {
                Nightwatch::dontSample();
            }
        });

        Event::listen(WebhookHandled::class, EnsureProfessionalMeteredItem::class);
    }
}
