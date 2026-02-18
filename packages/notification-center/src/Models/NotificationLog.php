<?php

namespace malikad778\NotificationCenter\Models;

use malikad778\NotificationCenter\Enums\NotificationChannel;
use malikad778\NotificationCenter\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\NotificationLogFactory::new();
    }
    protected $fillable = [
        'notification_id', 'channel', 'status',
        'sent_at', 'failed_at', 'error_message', 'metadata'
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'status' => NotificationStatus::class,
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}
