<?php

namespace malikad778\NotificationCenter\Models;

use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasUuids, HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\NotificationFactory::new();
    }

    protected $fillable = [
        'type', 'notifiable_type', 'notifiable_id', 
        'data', 'status', 'priority', 'notification_group_id', 
        'scheduled_at', 'sent_at', 'failed_at', 'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'read_at' => 'datetime',
        'status' => NotificationStatus::class,
        'priority' => NotificationPriority::class,
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
