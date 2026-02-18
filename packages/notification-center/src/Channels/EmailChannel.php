<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use App\Mail\GenericNotificationMail;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailChannel implements ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        $email = $user->getNotificationIdentifier('mail');

        if (!$email) {
            return new ChannelResult('mail', false, error: 'No email address');
        }

        try {
            Mail::to($email)->send(new GenericNotificationMail($payload));
            
            return new ChannelResult(
                channel: 'mail',
                success: true,
                messageId: 'mail_' . uniqid(),
                metadata: [
                    'recipient' => $email,
                    'timestamp' => now()->toIso8601String(),
                ]
            );
        } catch (Throwable $e) {
            return new ChannelResult(
                channel: 'mail',
                success: false,
                error: $e->getMessage(),
                metadata: [
                    'recipient' => $email,
                    'exception' => get_class($e),
                ]
            );
        }
    }

    public function isAvailable(Notifiable $user): bool
    {
        return !empty($user->getNotificationIdentifier('mail'));
    }
}
