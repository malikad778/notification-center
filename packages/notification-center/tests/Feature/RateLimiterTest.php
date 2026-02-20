<?php

namespace malikad778\NotificationCenter\Tests\Feature;

use malikad778\NotificationCenter\Tests\TestCase;
use malikad778\NotificationCenter\Services\NotificationRateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Auth\User;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_limiter_checks_limits()
    {
        $user = new class extends User implements Notifiable {
            protected $table = 'users';
            public $id = 1;
            public function getAuthIdentifier() { return $this->id; }
            public function routeNotificationFor($channel) { return 'test'; }
            public function getNotificationPreferences(): array { return []; }
            public function isInQuietHours(?string $channel = null): bool { return false; }
        };

        $limiter = app(NotificationRateLimiter::class);
        
        // Mock the limiter facade for testing
        RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false, true);
        RateLimiter::shouldReceive('hit')->andReturn(true);

        $this->assertTrue($limiter->check($user, 'mail'));
        $this->assertFalse($limiter->check($user, 'mail')); // second call returns true from mock
    }
}
