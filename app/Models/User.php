<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use malikad778\NotificationCenter\Traits\HasNotifications;

class User extends Authenticatable implements \malikad778\NotificationCenter\Contracts\Notifiable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasNotifications;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'plan',
        'has_mobile_app',
        'fcm_token',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNotificationIdentifier(string $channel): ?string
    {
        return match($channel) {
            'mail' => $this->email,
            'sms', 'whatsapp' => $this->phone_number,
            'push' => $this->fcm_token,
            default => null,
        };
    }
}
