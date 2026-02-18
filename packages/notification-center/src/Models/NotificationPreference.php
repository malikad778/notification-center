<?php

namespace malikad778\NotificationCenter\Models;

use malikad778\NotificationCenter\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\NotificationPreferenceFactory::new();
    }
    protected $fillable = [
        'user_id', 'channel', 'enabled',
        'quiet_hours_start', 'quiet_hours_end',
        'frequency_limit'
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'enabled' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('notification-center.user_model', 'App\Models\User'));
    }
}
