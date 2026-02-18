<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Jobs\SendNotificationJob;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

class NotificationBatchService
{
    /**
     * Dispatch a batch of notifications.
     *
     * @param Collection<int, Notifiable>|array<int, Notifiable> $users
     */
    public function dispatchBatch(
        array|Collection $users,
        NotificationPayload $payload,
        NotificationPriority $priority = NotificationPriority::Normal,
        ?string $batchName = null
    ): Batch {
        $jobs = [];

        foreach ($users as $user) {
            $jobs[] = new \malikad778\NotificationCenter\Jobs\SendNotificationJob($user, 'mail', $payload); // Simple default or use dispatcher
        }

        return Bus::batch($jobs)
            ->name($batchName ?? 'Bulk Notification ' . now()->toDateTimeString())
            ->allowFailures()
            ->onQueue('notifications')
            ->dispatch();
    }
}
