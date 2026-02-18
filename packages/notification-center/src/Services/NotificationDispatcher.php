<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationDispatcher
{
    public function __construct(
        protected NotificationRouter $router,
        protected FallbackResolver $fallbackResolver,
        protected NotificationGrouper $grouper
    ) {}

    /**
     * Dispatch notification to all resolved channels in parallel.
     * 
     * @return array<string, ChannelResult>
     */
    public function dispatch(Notifiable $user, NotificationPayload $payload, NotificationPriority $priority = NotificationPriority::Normal): array
    {
        // 1. Resolve Channels via Router (Pennant)
        $channels = $this->router->resolve($user);

        // 2. Quiet Hours filtering
        if ($user->isInQuietHours() && $priority !== NotificationPriority::Urgent) {
            $allowed = ['database', 'mail'];
            $channels = array_intersect($channels, $allowed);
            Log::info("Quiet hours active for user {$user->getAuthIdentifier()}. Restricted channels.");
        }

        if (empty($channels)) {
            return [];
        }

        // 3. Grouping
        $groupId = $this->grouper->handle($user, $payload);

        // 4. Persist "Master" Notification Record
        // (Required for logs and Database channel)
        $notification = \malikad778\NotificationCenter\Models\Notification::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\GenericNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'title' => $payload->title,
                'body' => $payload->body,
                'actionUrl' => $payload->actionUrl,
                'imageUrl' => $payload->imageUrl,
                'data' => $payload->data,
            ],
            'notification_group_id' => $groupId,
            'read_at' => null, // Unread by default
        ]);

        // 5. Prepare Tasks for Concurrency
        $tasks = [];
        foreach ($channels as $channel) {
            $tasks[] = fn () => $this->processChannel($user, $channel, $payload, $notification->id);
        }

        // 6. Run in Parallel
        try {
            $results = Concurrency::run($tasks);
        } catch (Throwable $e) {
            Log::warning("Concurrency failed, falling back to sync: " . $e->getMessage());
            $results = [];
            foreach ($tasks as $task) {
                $results[] = $task();
            }
        }

        // 7. Map results and Fire Events in Parent Process (for Pulse metrics)
        $keyedResults = [];
        foreach ($channels as $index => $channel) {
            $result = $results[$index] ?? new ChannelResult($channel, false, error: 'Internal Error');
            $keyedResults[$channel] = $result;

            if ($result->success) {
                event(new \malikad778\NotificationCenter\Events\NotificationSent($notification, $result));
            } else {
                event(new \malikad778\NotificationCenter\Events\NotificationFailed($notification, $result));
            }
        }

        return $keyedResults;
    }

    /**
     * Process a single channel, including fallback logic.
     * This runs inside the concurrent closure.
     */
    protected function processChannel(Notifiable $user, string $channelName, NotificationPayload $payload, string $notificationId): ChannelResult
    {
        try {
            $registry = config('notification-center.channels');
            $class = $registry[$channelName] ?? null;
            
            if (!$class) {
                return new ChannelResult($channelName, false, error: 'Channel class not found');
            }
            
            $limiter = app(\malikad778\NotificationCenter\Services\NotificationRateLimiter::class);
            if (!$limiter->check($user, $channelName)) {
                Log::warning("Rate limit exceeded for user {$user->getAuthIdentifier()} on channel {$channelName}");
                return new ChannelResult($channelName, false, error: 'Rate limit exceeded');
            }

            $channelInstance = app($class);
            $result = $channelInstance->send($payload, $user);

            if ($result->success) {
                $limiter->increment($user, $channelName);
                
                \malikad778\NotificationCenter\Models\NotificationLog::create([
                    'notification_id' => $notificationId,
                    'channel' => $channelName,
                    'status' => \malikad778\NotificationCenter\Enums\NotificationStatus::Sent,
                    'sent_at' => now(),
                    'metadata' => ['message_id' => $result->messageId]
                ]);
                
                return $result;
            }

            $fallbackResolver = app(FallbackResolver::class);
            $backupChannelName = $fallbackResolver->resolve($user, $channelName);
            
            if ($backupChannelName) {
                Log::info("Triggering fallback from {$channelName} to {$backupChannelName} for user {$user->getAuthIdentifier()}");
                
                $backupClass = $registry[$backupChannelName] ?? null;
                if ($backupClass) {
                    $backupInstance = app($backupClass);
                    $backupResult = $backupInstance->send($payload, $user);
                    
                    if ($backupResult->success) {
                        \malikad778\NotificationCenter\Models\NotificationLog::create([
                            'notification_id' => $notificationId,
                            'channel' => $channelName . '_fallback_' . $backupChannelName,
                            'status' => \malikad778\NotificationCenter\Enums\NotificationStatus::Sent,
                            'sent_at' => now(),
                            'metadata' => ['original_channel' => $channelName]
                        ]);
                        
                        return new ChannelResult($channelName, true, messageId: 'fallback-' . $backupResult->messageId);
                    }
                }
            }

            \malikad778\NotificationCenter\Models\NotificationLog::create([
                'notification_id' => $notificationId,
                'channel' => $channelName,
                'status' => \malikad778\NotificationCenter\Enums\NotificationStatus::Failed,
                'failed_at' => now(),
                'error_message' => $result->error,
            ]);
            
            return $result;

        } catch (Throwable $e) {
            \malikad778\NotificationCenter\Models\NotificationLog::create([
                'notification_id' => $notificationId,
                'channel' => $channelName,
                'status' => \malikad778\NotificationCenter\Enums\NotificationStatus::Failed,
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            
            $errorResult = new ChannelResult($channelName, false, error: $e->getMessage());
            
            return $errorResult;
        }
    }
}
