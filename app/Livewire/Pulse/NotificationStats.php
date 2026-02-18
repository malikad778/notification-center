<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;
use Laravel\Pulse\Pulse;
use Livewire\Attributes\Lazy;

#[Lazy]
class NotificationStats extends Card
{
    public function render(Pulse $pulse)
    {
        // Fetch aggregated stats for 'notification_send_stats'
        // type = channel name (email, sms, etc)
        // agg = count
        
        $stats = $pulse->aggregate(
            'notification_send_stats',
            ['count'],
            $this->periodAsInterval(),
        );

        return view('livewire.pulse.notification-stats', [
            'stats' => $stats,
        ]);
    }
}
