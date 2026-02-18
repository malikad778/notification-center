<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SlackChannel implements ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        
        $webhookUrl = $this->getWebhookUrl($user);

        if (!$webhookUrl) {
            return new ChannelResult('slack', false, error: 'No Slack webhook URL configured for user');
        }

        try {
            $response = Http::post($webhookUrl, [
                'text' => "*{$payload->title}*\n{$payload->body}",
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => "*{$payload->title}*\n{$payload->body}",
                        ],
                    ],
                    $payload->actionUrl ? [
                        'type' => 'actions',
                        'elements' => [
                            [
                                'type' => 'button',
                                'text' => [
                                    'type' => 'plain_text',
                                    'text' => 'View Details',
                                ],
                                'url' => $payload->actionUrl,
                            ],
                        ],
                    ] : [],
                ],
            ]);

            if ($response->successful()) {
                return new ChannelResult('slack', true);
            }

            return new ChannelResult('slack', false, error: "Slack API error: {$response->status()}");

        } catch (Throwable $e) {
            Log::error("Slack send failed: {$e->getMessage()}");
            return new ChannelResult('slack', false, error: $e->getMessage());
        }
    }

    public function isAvailable(Notifiable $user): bool
    {
        return !empty($this->getWebhookUrl($user));
    }

    protected function getWebhookUrl(Notifiable $user): ?string
    {
        return config('services.slack.webhook_url');
    }
}
