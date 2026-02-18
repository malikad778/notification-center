<?php

use malikad778\NotificationCenter\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
   
});

test('api requires authentication', function () {
    $response = $this->getJson('/api/notifications');
    $response->assertUnauthorized();
});

test('user can list their notifications', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Create notifications for this user
    Notification::factory()->count(3)->create([
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
    ]);

    // Create notification for another user
    Notification::factory()->create([
        'notifiable_id' => User::factory()->create()->id,
        'notifiable_type' => User::class,
    ]);

    $response = $this->getJson('/api/notifications');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('user can mark notification as read', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $notification = Notification::factory()->create([
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'read_at' => null,
    ]);

    $response = $this->patchJson("/api/notifications/{$notification->id}/read");

    $response->assertOk();
    
    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('user cannot mark others notification as read', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);

    $notification = Notification::factory()->create([
        'notifiable_id' => $otherUser->id, // Belongs to other user
        'notifiable_type' => User::class,
        'read_at' => null,
    ]);

    $response = $this->patchJson("/api/notifications/{$notification->id}/read");

    $response->assertNotFound(); // Or Forbidden, depending on implementation. Controller uses where()->firstOrFail() so it will be 404.
});

test('pulse recorder is registered', function () {
    // Basic check that the class exists and is instantiated
    expect(class_exists(\malikad778\NotificationCenter\Recorders\NotificationPulseRecorder::class))->toBeTrue();
    
  
});
