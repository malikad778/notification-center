<?php

namespace App\Features;

use App\Models\User;
use malikad778\NotificationCenter\Models\NotificationPreference;

class ReceiveSms
{
    public function resolve(User $user): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('channel', 'sms')
            ->first();

        return $pref ? $pref->enabled : true;
    }
}
