<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'testuser@example.com'],
        ['name' => 'Test User', 'password' => bcrypt('password')]
    );

    $payload = new \App\DTOs\NotificationPayload(
        title: 'Hello from Laravel 12!',
        body: 'This is a test notification sent via EmailChannel.',
        actionUrl: url('/'),
        data: ['foo' => 'bar']
    );

    $channel = new \App\NotificationCenter\Channels\EmailChannel();
    $result = $channel->send($payload, $user);

    return response()->json($result);
});
