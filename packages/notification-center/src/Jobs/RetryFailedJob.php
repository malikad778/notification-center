<?php

namespace malikad778\NotificationCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use malikad778\NotificationCenter\Models\NotificationLog;
use malikad778\NotificationCenter\Enums\NotificationStatus;
use malikad778\NotificationCenter\Models\Notification;
use malikad778\NotificationCenter\DTOs\NotificationPayload;

class RetryFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $notificationLogId
    ) {}

    public function handle(): void
    {
        $log = NotificationLog::find($this->notificationLogId);
        
        if (!$log || $log->status !== NotificationStatus::Failed) {
            return;
        }

        $notification = Notification::find($log->notification_id);
        
        if (!$notification) {
            return;
        }

        $payloadData = $notification->data; // Contains payload data
        $payload = new NotificationPayload(
            title: $payloadData['title'] ?? '',
            body: $payloadData['body'] ?? '',
            data: $payloadData['data'] ?? [],
            actionUrl: $payloadData['actionUrl'] ?? null,
            imageUrl: $payloadData['imageUrl'] ?? null,
            groupKey: $payloadData['groupKey'] ?? null,
            groupLabel: $payloadData['groupLabel'] ?? null,
        );

        SendNotificationJob::dispatch(
            $notification->notifiable,
            $log->channel,
            $payload,
            $notification->id
        );
    }
}
