<?php

namespace malikad778\NotificationCenter\Recorders;

use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use malikad778\NotificationCenter\Events\NotificationSent;
use malikad778\NotificationCenter\Events\NotificationFailed;

class NotificationPulseRecorder
{
    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {
    }

    public function record(object $event): void
    {
        if (!($event instanceof NotificationSent || $event instanceof NotificationFailed)) {
             return;
        }
        $status = $event instanceof NotificationSent ? 'sent' : 'failed';
        $channel = $event->result->channel;

        $this->pulse->record(
            'notification_send_stats',
            $channel . ':' . $status,
            1
        );
    }
}
