<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Log;
use Throwable;
use Twilio\Rest\Client;

class WhatsAppChannel implements ChannelInterface
{
    protected ?Client $client = null;

    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        $phoneNumber = $user->getNotificationIdentifier('whatsapp');

        if (!$phoneNumber) {
            return new ChannelResult('whatsapp', false, error: 'No phone number');
        }

        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.auth_token');
            $from = config('services.twilio.whatsapp_number'); // e.g., "whatsapp:+14155238886"

            if (!$sid || !$token || !$from) {
                return new ChannelResult('whatsapp', false, error: 'Twilio WhatsApp credentials not configured');
            }

            if (!$this->client) {
                $this->client = new Client($sid, $token);
            }

            $to = str_starts_with($phoneNumber, 'whatsapp:') 
                ? $phoneNumber 
                : 'whatsapp:' . $phoneNumber;

            $message = $this->client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $payload->body,
                ]
            );

            return new ChannelResult('whatsapp', true, messageId: $message->sid);

        } catch (Throwable $e) {
            Log::error("WhatsApp send failed: {$e->getMessage()}");
            return new ChannelResult('whatsapp', false, error: $e->getMessage());
        }
    }

    public function isAvailable(Notifiable $user): bool
    {
        return !empty($user->getNotificationIdentifier('whatsapp'));
    }
}
