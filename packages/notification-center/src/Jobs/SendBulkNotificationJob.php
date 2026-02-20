<?php

namespace malikad778\NotificationCenter\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Services\NotificationRouter;
use Illuminate\Support\Facades\Bus;

class SendBulkNotificationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $users,
        public NotificationPayload $payload,
        public array $channels = []
    ) {}

    public function handle(NotificationRouter $router): void
    {
        $jobs = [];
        
        foreach ($this->users as $user) {
            $userChannels = empty($this->channels) ? $router->resolve($user) : $this->channels;
            
            foreach ($userChannels as $channel) {
                $jobs[] = new SendNotificationJob($user, $channel, $this->payload);
            }
        }

        if (count($jobs) > 0) {
            // Depending on how this job is dispatched, we might be building a batch here
            // or we might want to just dispatch them directly.
            // Since the requirements specify using Bus::batch() for bulk sending:
            Bus::batch($jobs)
                ->name('Bulk Notification Batch - ' . now()->toDateTimeString())
                ->allowFailures()
                ->onQueue('notifications')
                ->dispatch();
        }
    }
}
