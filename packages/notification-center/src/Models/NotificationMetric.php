<?php

namespace malikad778\NotificationCenter\Models;

use malikad778\NotificationCenter\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;

class NotificationMetric extends Model
{
    protected $fillable = [
        'notification_type', 'channel',
        'sent_count', 'delivered_count', 'failed_count', 'opened_count',
        'date'
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'date' => 'date',
    ];
}
