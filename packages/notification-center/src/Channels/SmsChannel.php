<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Log;
use Throwable;
use Twilio\Rest\Client;

class SmsChannel implements ChannelInterface
{
    protected ?Client $client = null;

    public function __construct()
    {
        // Lazy initialization handled in send() to avoid overhead if not used
    }

    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        $phoneNumber = $user->getNotificationIdentifier('sms');

        if (!$phoneNumber) {
            return new ChannelResult('sms', false, error: 'No phone number');
        }

        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.auth_token');
            $from = config('services.twilio.phone_number');

            if (!$sid || !$token || !$from) {
                return new ChannelResult('sms', false, error: 'Twilio credentials not configured');
            }

            // Lazy init
            if (!$this->client) {
                $this->client = new Client($sid, $token);
            }

            $message = $this->client->messages->create(
                $phoneNumber,
                [
                    'from' => $from,
                    'body' => $payload->body,
                ]
            );

            return new ChannelResult('sms', true, messageId: $message->sid);

        } catch (Throwable $e) {
            Log::error("SMS send failed: {$e->getMessage()}");
            return new ChannelResult('sms', false, error: $e->getMessage());
        }
    }

    public function isAvailable(Notifiable $user): bool
    {
        return !empty($user->getNotificationIdentifier('sms'));
    }
}
