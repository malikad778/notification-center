<?php

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use App\Mail\GenericNotificationMail;
use App\Models\User;
use malikad778\NotificationCenter\Channels\EmailChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('email channel sends mail with correct payload', function () {
    Mail::fake();

    $user = User::factory()->create(['email' => 'test@example.com']);
    
    $payload = new NotificationPayload(
        title: 'Test Notification',
        body: 'This is a test body.',
        actionUrl: 'https://example.com'
    );

    $channel = new EmailChannel();
    $result = $channel->send($payload, $user);

    expect($result->success)->toBeTrue()
        ->and($result->channel)->toBe('email');

    Mail::assertSent(GenericNotificationMail::class, function ($mail) use ($user, $payload) {
        return $mail->hasTo($user->email) &&
               $mail->payload->title === $payload->title;
    });
});

test('email channel handles exceptions gracefullly', function () {
    Mail::shouldReceive('to')->andThrow(new Exception('SMTP Error'));

    $user = User::factory()->create(['email' => 'fail@example.com']);
    $payload = new NotificationPayload('Fail', 'Body');

    $channel = new EmailChannel();
    $result = $channel->send($payload, $user);

    expect($result->success)->toBeFalse()
        ->and($result->error)->toBe('SMTP Error');
});
