<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\Enums\NotificationChannel;
use App\Features\ReceiveBroadcast;
use App\Features\ReceiveDatabase;
use App\Features\ReceiveEmail;
use App\Features\ReceiveSms;
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
        // Map channel names to Feature classes
        $featureMap = [
            NotificationChannel::Mail->value => ReceiveEmail::class,
            NotificationChannel::Sms->value => ReceiveSms::class,
            NotificationChannel::Database->value => ReceiveDatabase::class,
            NotificationChannel::Broadcast->value => ReceiveBroadcast::class,
        ];

        // If no specifically requested channels, check all available in the map
        $candidates = empty($requestedChannels) ? array_keys($featureMap) : $requestedChannels;
        $activeChannels = [];

        foreach ($candidates as $channel) {
            $featureClass = $featureMap[$channel] ?? null;

            if ($featureClass && Feature::for($user)->active($featureClass)) {
                $activeChannels[] = $channel;
            } elseif (!$featureClass) {
                // If no feature toggle exists for a channel, assume it's valid if registered
                // OR strict mode: ignore it. We'll assume strict pennant control here.
                // For now, let's allow it if it's in our Enum but has no feature class (future proofing)
                if (NotificationChannel::tryFrom($channel)) {
                    $activeChannels[] = $channel;
                }
            }
        }

        return $activeChannels;
    }
}
