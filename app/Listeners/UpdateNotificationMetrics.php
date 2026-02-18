<?php

namespace App\Listeners;

use malikad778\NotificationCenter\Events\NotificationFailed;
use malikad778\NotificationCenter\Events\NotificationSent;
use malikad778\NotificationCenter\Models\NotificationMetric;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

class UpdateNotificationMetrics implements ShouldQueue
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            NotificationSent::class,
            [self::class, 'handleSent']
        );

        $events->listen(
            NotificationFailed::class,
            [self::class, 'handleFailed']
        );
    }

    public function handleSent(NotificationSent $event): void
    {
        // Access channel from the Result object, not the Notification model
        $this->updateMetric($event->result->channel, 'sent_count');
    }

    public function handleFailed(NotificationFailed $event): void
    {
        $this->updateMetric($event->result->channel, 'failed_count');
    }

    protected function updateMetric(string $channel, string $column): void
    {
        $date = now()->toDateString();
        
        try {
            $metric = NotificationMetric::firstOrCreate(
                [
                    'date' => $date,
                    'channel' => $channel,
                    'notification_type' => 'general', 
                ],
                [
                    'sent_count' => 0,
                    'failed_count' => 0,
                    'delivered_count' => 0,
                    'opened_count' => 0,
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Error 23000 is Integrity constraint violation (Duplicate entry)
            // If strictly 23000 or (SQLite 19), we assume it exists now.
            if ($e->getCode() === '23000' || $e->getCode() === 23000 || str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                 // Sometmes in tests with transactions, we can get unique violation but cannot find the record.
                 // We try to increment blindly. If it updates 0 rows, so be it.
                 NotificationMetric::where('date', $date)
                    ->where('channel', $channel)
                    ->where('notification_type', 'general')
                    ->increment($column);
                 return;
            } else {
                throw $e;
            }
        }

        $metric->increment($column);
    }
}
