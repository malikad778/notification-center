<?php

namespace Tests\Feature;

use malikad778\NotificationCenter\Models\NotificationTemplate;
use App\Models\User;
use malikad778\NotificationCenter\Services\NotificationDispatcher;
use App\Services\NotificationTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_service_replaces_placeholders_and_dispatches()
    {
        // Arrange
        $user = User::factory()->create();
        
        NotificationTemplate::create([
            'name' => 'welcome_email',
            'subject' => 'Welcome {{ name }}!',
            'body' => 'Hello {{ name }}, your ID is {{ id }}.',
            'channels' => ['email'],
            'metadata' => ['campaign' => 'onboarding']
        ]);

        $dispatcher = Mockery::mock(NotificationDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(
                Mockery::on(fn($u) => $u->id === $user->id),
                Mockery::on(fn($p) => 
                    $p->title === "Welcome {$user->name}!" &&
                    str_contains($p->body, "your ID is {$user->id}") &&
                    $p->data['campaign'] === 'onboarding'
                )
            );

        $service = new \malikad778\NotificationCenter\Services\NotificationTemplateService($dispatcher);

        // Act
        $service->send($user, 'welcome_email', ['name' => $user->name, 'id' => $user->id]);
    }
}
