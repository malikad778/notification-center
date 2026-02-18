<?php

namespace malikad778\NotificationCenter\Events;

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationPushed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public NotificationPayload $payload,
        public User $user
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->payload->title,
            'body' => $this->payload->body,
            'data' => $this->payload->data,
            'actionUrl' => $this->payload->actionUrl,
            'imageUrl' => $this->payload->imageUrl,
        ];
    }
}
