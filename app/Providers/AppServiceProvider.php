<?php

namespace App\Providers;

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
        \Laravel\Pennant\Feature::define('receive-email', fn ($user) => true);
        \Laravel\Pennant\Feature::define('receive-sms', fn ($user) => true);
        \Laravel\Pennant\Feature::define('receive-database', fn ($user) => true);
        \Laravel\Pennant\Feature::define('receive-broadcast', fn ($user) => true);
        \Laravel\Pennant\Feature::define('receive-whatsapp', fn ($user) => $user->plan === 'premium');
        \Laravel\Pennant\Feature::define('receive-push', fn ($user) => $user->has_mobile_app ?? false);

        // Register Metrics Listener
        \Illuminate\Support\Facades\Event::subscribe(\App\Listeners\UpdateNotificationMetrics::class);
    }
}
