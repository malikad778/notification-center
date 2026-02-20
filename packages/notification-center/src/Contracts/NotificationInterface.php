<?php

namespace malikad778\NotificationCenter\Contracts;

use malikad778\NotificationCenter\DTOs\NotificationPayload;

interface NotificationInterface
{
    public function toPayload(): NotificationPayload;
    
    public function via(Notifiable $notifiable): array;
}
