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
        $stats = $pulse->aggregate(
            'notification_send_stats',
            ['count'],
            $this->periodAsInterval(),
        );

        // Fallback for local development where aggregation haven't run yet
        if ($stats->isEmpty()) {
            $stats = \Illuminate\Support\Facades\DB::table('pulse_entries')
                ->where('type', 'notification_send_stats')
                ->where('timestamp', '>=', now()->sub($this->periodAsInterval())->getTimestamp())
                ->groupBy('key')
                ->select('key', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->get();
        }

        return view('livewire.pulse.notification-stats', [
            'stats' => $stats,
        ]);
    }
}
