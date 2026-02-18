<?php

namespace malikad778\NotificationCenter\Enums;

enum NotificationChannel: string
{
    case Database = 'database';
    case Mail = 'mail';
    case Sms = 'sms';
    case Broadcast = 'broadcast';
    case Slack = 'slack';
    case Fcm = 'fcm';
}
