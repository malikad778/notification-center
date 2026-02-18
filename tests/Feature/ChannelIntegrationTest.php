<?php

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Jobs\SendNotificationJob;
use App\Models\User;
use malikad778\NotificationCenter\Channels\DatabaseChannel;
use malikad778\NotificationCenter\Channels\SmsChannel;
use malikad778\NotificationCenter\Services\NotificationRateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('sms channel sends message using lazy client', function () {
    if (!config('services.twilio.sid')) {
        $this->markTestSkipped('Twilio credentials not configured.');
    }

    $user = User::factory()->create(['phone_number' => '1234567890']);
    $payload = new NotificationPayload('Test SMS', 'Body content');
    
    $channel = new SmsChannel();
    $result = $channel->send($payload, $user);

    expect($result->success)->toBeTrue()
        ->and($result->channel)->toBe('sms')
        ->and($result->messageId)->not->toBeNull();
});

test('sms channel fails if no phone number', function () {
    $user = User::factory()->create(['phone_number' => null]);
    $payload = new NotificationPayload('Test', 'Body');
    
    $channel = new SmsChannel();
    $result = $channel->send($payload, $user);

    expect($result->success)->toBeFalse()
        ->and($result->error)->toBe('No phone number');
});

test('database channel acknowledges receipt', function () {
    $user = User::factory()->create();
    $payload = new NotificationPayload('Test', 'Body');
    
    $channel = new DatabaseChannel();
    $result = $channel->send($payload, $user);

    expect($result->success)->toBeTrue()
        ->and($result->channel)->toBe('database');
});

test('rate limiter blocks excessive requests', function () {
    $user = User::factory()->create();
    $limiter = new NotificationRateLimiter(defaultLimit: 2);
    $channel = 'sms';

    // 1st request - should pass
    expect($limiter->check($user, $channel))->toBeTrue();
    $limiter->increment($user, $channel);

    // 2nd request - should pass
    expect($limiter->check($user, $channel))->toBeTrue();
    $limiter->increment($user, $channel);

    // 3rd request - should fail
    expect($limiter->check($user, $channel))->toBeFalse();
});

test('send notification job processes successfully', function () {
    Queue::fake();
    
    $user = User::factory()->create(['phone_number' => '555']);
    $payload = new NotificationPayload('Job Test', 'Body');

    // Mock the channel in the container
    $mock = Mockery::mock(SmsChannel::class);
    $mock->shouldReceive('isAvailable')->andReturnTrue();
    $mock->shouldReceive('send')->once()->andReturn(new \malikad778\NotificationCenter\DTOs\ChannelResult('sms', true, messageId: 'mock-id'));
    $this->app->instance(SmsChannel::class, $mock);
    
    $job = new SendNotificationJob($user, 'sms', $payload);
    $limiter = new NotificationRateLimiter();
    
    // Manually run handle to verify logic (since Queue is faked)
    $job->handle($limiter);
    
    // If no exception, it succeeded.
    expect(true)->toBeTrue(); 
});
