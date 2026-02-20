<?php

namespace malikad778\NotificationCenter\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannelConfig extends Model
{
    protected $table = 'notification_channels';

    protected $fillable = [
        'channel_name',
        'is_active',
        'priority',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'config' => 'encrypted:array', // Storing encrypted API keys
    ];
}
