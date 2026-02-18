<?php

namespace malikad778\NotificationCenter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use malikad778\NotificationCenter\Services\NotificationDispatcher;
use malikad778\NotificationCenter\Services\NotificationRouter;
use malikad778\NotificationCenter\Services\NotificationRateLimiter;
use malikad778\NotificationCenter\Services\NotificationTemplateService;
use malikad778\NotificationCenter\Services\NotificationBatchService;
use malikad778\NotificationCenter\Services\NotificationGrouper;
use malikad778\NotificationCenter\Services\FallbackResolver;
use malikad778\NotificationCenter\Recorders\NotificationPulseRecorder;
use Laravel\Pulse\Pulse;

class NotificationCenterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/notification-center.php' => config_path('notification-center.php'),
            ], 'notification-center-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'notification-center-migrations');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notification-center');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/notification-center'),
            ], 'notification-center-views');
        }

        if ($this->app->bound(Pulse::class)) {
            $this->app->make(Pulse::class)->register([
                NotificationPulseRecorder::class => [
                    \malikad778\NotificationCenter\Events\NotificationSent::class,
                    \malikad778\NotificationCenter\Events\NotificationFailed::class,
                ],
            ]);

            // Ensure events are handled even in CLI/Worker contexts
            $events = $this->app->make(\Illuminate\Contracts\Events\Dispatcher::class);
            $events->listen(\malikad778\NotificationCenter\Events\NotificationSent::class, [NotificationPulseRecorder::class, 'record']);
            $events->listen(\malikad778\NotificationCenter\Events\NotificationFailed::class, [NotificationPulseRecorder::class, 'record']);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notification-center.php', 'notification-center');

        $this->app->singleton(NotificationDispatcher::class);
        $this->app->singleton(NotificationRouter::class);
        $this->app->singleton(NotificationRateLimiter::class);
        $this->app->singleton(NotificationBatchService::class);
        $this->app->singleton(NotificationTemplateService::class);
        $this->app->singleton(NotificationGrouper::class);
        $this->app->singleton(FallbackResolver::class);
    }
}
