<?php

namespace malikad778\NotificationCenter\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\NotificationTemplateFactory::new();
    }
    protected $fillable = [
        'name', 'subject', 'body', 
        'channels', 'metadata'
    ];

    protected $casts = [
        'channels' => 'array',
        'metadata' => 'array',
    ];
}
