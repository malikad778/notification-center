<?php

namespace malikad778\NotificationCenter\Listeners;

use malikad778\NotificationCenter\Events\NotificationSent;
use malikad778\NotificationCenter\Events\NotificationFailed;
use malikad778\NotificationCenter\Models\NotificationLog;
use malikad778\NotificationCenter\Enums\NotificationStatus;

class LogNotificationResult
{
    public function handle(NotificationSent|NotificationFailed $event): void
    {
        $status = $event instanceof NotificationSent 
            ? NotificationStatus::Sent 
            : NotificationStatus::Failed;

        $metadata = [];
        $error = null;

        if ($event instanceof NotificationSent && isset($event->result->messageId)) {
            $metadata['message_id'] = $event->result->messageId;
        }

        if ($event instanceof NotificationFailed && isset($event->result->error)) {
            $error = $event->result->error;
        }

        $event->notification->update([
            'channel' => $event->result->channel,
            'status' => $status,
            'sent_at' => $status === NotificationStatus::Sent ? now() : $event->notification->sent_at,
            'failed_at' => $status === NotificationStatus::Failed ? now() : $event->notification->failed_at,
            'error_message' => $error,
            'attempts' => \Illuminate\Support\Facades\DB::raw('attempts + 1'),
        ]);
        
        // Remove or keep metadata handling in payload/external table if required, 
        // but spec only asked for payload, attempts, etc. in notifications.
    }
}
