<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\Enums\NotificationChannel;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Laravel\Pennant\Feature;

class NotificationRouter
{
    /**
     * Resolve valid channels for the user based on preferences (Pennant Features).
     * 
     * @return array<string>
     */
    public function resolve(Notifiable $user, array $requestedChannels = []): array
    {
        // Map channel names to Feature strings
        $featureMap = [
            NotificationChannel::Mail->value => 'receive-email',
            NotificationChannel::Sms->value => 'receive-sms',
            NotificationChannel::Database->value => 'receive-database',
            NotificationChannel::Broadcast->value => 'receive-broadcast',
            NotificationChannel::WhatsApp->value => 'receive-whatsapp',
            NotificationChannel::Push->value => 'receive-push',
        ];

        // If no specifically requested channels, check all available in the map
        $candidates = empty($requestedChannels) ? array_keys($featureMap) : $requestedChannels;
        $activeChannels = [];

        // Check user preferences
        $preferences = method_exists($user, 'notificationPreferences') 
            ? $user->notificationPreferences()->get()->keyBy('channel')
            : collect();

        foreach ($candidates as $channel) {
            // Respect opt-out preference
            if ($preferences->has($channel) && !$preferences->get($channel)->enabled) {
                continue;
            }

            $featureName = $featureMap[$channel] ?? null;

            if ($featureName && Feature::for($user)->active($featureName)) {
                $activeChannels[] = $channel;
            } elseif (!$featureName) {
                // If no feature toggle exists for a channel, assume it's valid if registered
                if (NotificationChannel::tryFrom($channel)) {
                    $activeChannels[] = $channel;
                }
            }
        }

        return $activeChannels;
    }
}
