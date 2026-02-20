<?php

namespace malikad778\NotificationCenter\Tests\Unit;

use malikad778\NotificationCenter\Tests\TestCase;
use malikad778\NotificationCenter\Services\NotificationRouter;
use Laravel\Pennant\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Auth\User;
use malikad778\NotificationCenter\Enums\NotificationChannel;
use malikad778\NotificationCenter\Contracts\Notifiable;

class NotificationRouterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_active_channels_via_pennant()
    {
        // Setup mock user
        $user = new class extends User implements Notifiable {
            protected $table = 'users';
            public $id = 1;
            public $plan = 'premium';
            public $has_mobile_app = false;
            
            public function getAuthIdentifier() { return $this->id; }
            public function routeNotificationFor($channel) { return 'test'; }
            public function getNotificationPreferences(): array { return []; }
            public function isInQuietHours(?string $channel = null): bool { return false; }
        };

        Feature::define('receive-email', fn () => true);
        Feature::define('receive-sms', fn () => false);
        Feature::define('receive-whatsapp', fn () => $user->plan === 'premium');
        Feature::define('receive-push', fn () => false);

        $router = app(NotificationRouter::class);
        $channels = $router->resolve($user);

        $this->assertContains(NotificationChannel::Mail->value, $channels);
        $this->assertContains(NotificationChannel::WhatsApp->value, $channels);
        $this->assertNotContains(NotificationChannel::Sms->value, $channels);
        $this->assertNotContains(NotificationChannel::Push->value, $channels);
    }
}
