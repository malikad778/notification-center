<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Here you may define the channels available in your application.
    | Each channel must implement the ChannelInterface.
    |
    */
    'channels' => [
        'mail'     => \malikad778\NotificationCenter\Channels\EmailChannel::class,
        'sms' => \malikad778\NotificationCenter\Channels\SmsChannel::class,
        'database' => \malikad778\NotificationCenter\Channels\DatabaseChannel::class,
        'broadcast' => \malikad778\NotificationCenter\Channels\BroadcastChannel::class,
        'slack' => \malikad778\NotificationCenter\Channels\SlackChannel::class,
        'push' => \malikad778\NotificationCenter\Channels\PushChannel::class,
        'whatsapp' => \malikad778\NotificationCenter\Channels\WhatsAppChannel::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Chains
    |--------------------------------------------------------------------------
    |
    | Define the fallback behavior when a channel fails.
    |
    */
    'fallback_chain' => [
        'sms'   => ['mail'],
        'slack' => ['mail'],
        'push'  => ['mail', 'database'],
        'whatsapp' => ['sms', 'mail'],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The model class used for users/notifiables.
    |
    */
    'user_model' => \App\Models\User::class,
];
