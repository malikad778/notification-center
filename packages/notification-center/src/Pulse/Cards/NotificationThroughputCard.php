<?php

namespace malikad778\NotificationCenter\Pulse\Cards;

use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class NotificationThroughputCard extends Card
{
    public function render()
    {
        return view('notification-center::pulse.throughput');
    }
}
