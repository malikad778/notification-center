<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_preferences()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/notifications/preferences', [
            'channel' => 'mail',
            'enabled' => false,
            'frequency_limit' => 5
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'channel' => 'mail',
            'enabled' => 0,
            'frequency_limit' => 5
        ]);
    }

    public function test_user_can_get_preferences()
    {
        $user = User::factory()->create();
        
        \malikad778\NotificationCenter\Models\NotificationPreference::create([
            'user_id' => $user->id,
            'channel' => 'sms',
            'enabled' => true
        ]);

        $response = $this->actingAs($user)->getJson('/api/notifications/preferences');

        $response->assertOk()
            ->assertJsonFragment(['channel' => 'sms', 'enabled' => true]);
    }
}
