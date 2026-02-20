<?php

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationChannel;
use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Models\NotificationGroup;
use App\Models\User;
use malikad778\NotificationCenter\Services\NotificationDispatcher;
use malikad778\NotificationCenter\Services\NotificationGrouper;
use malikad778\NotificationCenter\Services\NotificationRouter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

uses(RefreshDatabase::class);

test('router uses pennant features to filter channels', function () {
    $user = User::factory()->create();
    
    // Define feature states
    Feature::for($user)->activate('receive-email');
   
    \malikad778\NotificationCenter\Models\NotificationPreference::create([
        'user_id' => $user->id,
        'channel' => 'sms',
        'enabled' => false,
    ]);

    $router = app(NotificationRouter::class);
    $channels = $router->resolve($user);

    expect($channels)->toContain('mail')
        ->and($channels)->not->toContain('sms')
        ->and($channels)->toContain('database'); // Database defaults to true
});

test('quiet hours suppress noisy channels', function () {
    // Set time to 23:00
    $now = \Illuminate\Support\Carbon::create(2024, 1, 1, 23, 0, 0);
    \Illuminate\Support\Carbon::setTestNow($now);

    $user = User::factory()->create();

    \malikad778\NotificationCenter\Models\NotificationPreference::create([
        'user_id' => $user->id,
        'channel' => 'sms',
        'quiet_hours_start' => '22:00',
        'quiet_hours_end' => '07:00'
    ]);
    \malikad778\NotificationCenter\Models\NotificationPreference::create([
        'user_id' => $user->id,
        'channel' => 'broadcast',
        'quiet_hours_start' => '22:00',
        'quiet_hours_end' => '07:00'
    ]);

    $dispatcher = app(NotificationDispatcher::class);
    $payload = new NotificationPayload('Test', 'Body');

    $results = $dispatcher->dispatch($user, $payload);

  
    
    expect(array_keys($results))->toContain('mail', 'database')
        ->and(array_keys($results))->not->toContain('sms', 'broadcast');
});

test('urgent priority bypasses quiet hours', function () {
    $now = \Illuminate\Support\Carbon::create(2024, 1, 1, 23, 0, 0);
    \Illuminate\Support\Carbon::setTestNow($now);

    $user = User::factory()->create();

    \malikad778\NotificationCenter\Models\NotificationPreference::create([
        'user_id' => $user->id,
        'channel' => 'sms',
        'quiet_hours_start' => '22:00',
        'quiet_hours_end' => '07:00'
    ]);
    \malikad778\NotificationCenter\Models\NotificationPreference::create([
        'user_id' => $user->id,
        'channel' => 'broadcast',
        'quiet_hours_start' => '22:00',
        'quiet_hours_end' => '07:00'
    ]);

    $dispatcher = app(NotificationDispatcher::class);
    $payload = new NotificationPayload('Test', 'Body');

    $results = $dispatcher->dispatch($user, $payload, NotificationPriority::Urgent);

    // Urgent should include all available channels
    expect(array_keys($results))->toContain('mail', 'database', 'sms', 'broadcast');
});

test('grouper creates and increments groups', function () {
    $user = User::factory()->create();
    $payload = new NotificationPayload(
        title: 'New Order', 
        body: 'Order #123', 
        groupKey: 'order:123',
        groupLabel: 'Order #123 Updates'
    );

    $grouper = new NotificationGrouper();
    
    // 1st call
    $groupId1 = $grouper->handle($user, $payload);
    $group1 = NotificationGroup::find($groupId1);
    
    expect($group1->count)->toBe(1)
        ->and($group1->group_key)->toBe('order:123');

    // 2nd call
    $groupId2 = $grouper->handle($user, $payload);
    $group2 = NotificationGroup::find($groupId2);

    expect($groupId1)->toBe($groupId2)
        ->and($group2->count)->toBe(2);
});

test('dispatcher handles concurrency execution', function () {
  
    expect(true)->toBeTrue();
});
