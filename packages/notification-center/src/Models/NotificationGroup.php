<?php

namespace malikad778\NotificationCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationGroup extends Model
{
    protected $fillable = [
        'group_key', 'group_label', 
        'user_id', 'count'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
