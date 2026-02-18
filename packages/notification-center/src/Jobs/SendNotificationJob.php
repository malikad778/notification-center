<?php

namespace malikad778\NotificationCenter\Jobs;

use malikad778\NotificationCenter\Contracts\Notifiable;
use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationStatus;
use malikad778\NotificationCenter\Events\NotificationFailed;
use malikad778\NotificationCenter\Events\NotificationRateLimited;
use malikad778\NotificationCenter\Events\NotificationSending;
use malikad778\NotificationCenter\Events\NotificationSent;
use malikad778\NotificationCenter\Models\Notification;
use malikad778\NotificationCenter\Models\NotificationLog;
use malikad778\NotificationCenter\Channels\ChannelInterface;
use malikad778\NotificationCenter\Services\NotificationRateLimiter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    
    // Exponential backoff: 1min, 5min, 30min
    public function backoff(): array
    {
        return [60, 300, 1800];
    }

    public function __construct(
        public Notifiable $user,
        public string $channelName,
        public NotificationPayload $payload,
        public ?string $notificationId = null // Optional linkage to main record
    ) {}

    public function handle(NotificationRateLimiter $limiter): void
    {
        // 1. Resolve Channel
        $registry = config('notification-center.channels');
        $channelClass = $registry[$this->channelName] ?? null;

        if (!$channelClass || !class_exists($channelClass)) {
            Log::error("Channel {$this->channelName} not found.");
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = App::make($channelClass);

        // 2. Check Availability
        if (!$channel->isAvailable($this->user)) {
             return;
        }

        // 3. Rate Limit Check
        if (!$limiter->check($this->user, $this->channelName)) {
            // Fire event
            $notification = $this->notificationId ? Notification::find($this->notificationId) : null;
            if ($notification) {
                event(new NotificationRateLimited($notification));
            }
            
            // Release back to queue with delay? Or fail?
            // Usually we might want to delay execution.
            $this->release(3600); // Try again in an hour
            return;
        }

        $notification = $this->notificationId ? Notification::find($this->notificationId) : null;

        // 4. Send
        if ($notification) {
            event(new NotificationSending($notification));
        }
        
        try {
            $result = $channel->send($this->payload, $this->user);

            if ($result->success) {
                $limiter->increment($this->user, $this->channelName);
                
                // Log success
                if ($this->notificationId) {
                    NotificationLog::create([
                        'notification_id' => $this->notificationId,
                        'channel' => $this->channelName,
                        'status' => NotificationStatus::Sent,
                        'sent_at' => now(),
                        'metadata' => ['message_id' => $result->messageId]
                    ]);
                }

                if ($notification) {
                    event(new NotificationSent($notification, $result));
                }
            } else {
                throw new Exception($result->error ?? 'Unknown error');
            }
        } catch (Throwable $e) {
            // Log failure
            if ($this->notificationId) {
                NotificationLog::create([
                    'notification_id' => $this->notificationId,
                    'channel' => $this->channelName,
                    'status' => NotificationStatus::Failed,
                    'failed_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }
            
            if ($notification) {
                $errorResult = new ChannelResult($this->channelName, false, error: $e->getMessage());
                event(new NotificationFailed($notification, $errorResult));
            }
            
            throw $e;
        }
    }
}
