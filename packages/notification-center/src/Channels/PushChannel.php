<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushChannel implements ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        $deviceToken = $user->getNotificationIdentifier('push'); 

        if (!$deviceToken) {
             return new ChannelResult('push', false, error: 'No device token found');
        }

        try {
            
            $serverKey = config('services.fcm.key'); 
            
            if (!$serverKey) {
                 return new ChannelResult('push', false, error: 'FCM Server Key not configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $payload->title,
                    'body' => $payload->body,
                ],
                'data' => $payload->actionUrl ? ['action_url' => $payload->actionUrl] : [],
            ]);

            if ($response->successful()) {
                return new ChannelResult('push', true, messageId: $response->json('results.0.message_id'));
            }
            
            return new ChannelResult('push', false, error: "FCM Error: " . $response->body());

        } catch (Throwable $e) {
            Log::error("Push send failed: {$e->getMessage()}");
            return new ChannelResult('push', false, error: $e->getMessage());
        }
    }

    public function isAvailable(Notifiable $user): bool
    {
        return !empty($user->getNotificationIdentifier('push'));
    }
}
