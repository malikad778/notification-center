<?php

namespace malikad778\NotificationCenter\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Processing = 'processing';
}
