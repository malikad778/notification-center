<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\Contracts\Notifiable;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Jobs\SendNotificationJob;

class PendingNotification
{
    protected ?\DateTimeInterface $delay = null;
    protected array $channels = [];
    protected NotificationPriority $priority = NotificationPriority::Normal;
    protected bool $dispatched = false;

    public function __construct(
        protected NotificationDispatcher $dispatcher,
        protected Notifiable $to,
        protected NotificationPayload $notification
    ) {}

    public function delay(\DateTimeInterface|\DateInterval|int|null $delay): static
    {
        $this->delay = $delay;
        return $this;
    }

    public function via(array $channels): static
    {
        $this->channels = $channels;
        return $this;
    }
    
    public function priority(NotificationPriority $priority): static
    {
        $this->priority = $priority;
        return $this;
    }
    
    public function dispatch(): array
    {
        $this->dispatched = true;
        
        if ($this->delay) {
            // Since dispatch expects returning results, and we are delaying,
            // we will resolve the channels and queue the jobs.
            $router = app(NotificationRouter::class);
            $channels = empty($this->channels) ? $router->resolve($this->to) : $this->channels;
            
            foreach ($channels as $channel) {
                SendNotificationJob::dispatch(
                    $this->to,
                    $channel,
                    $this->notification
                )->delay($this->delay);
            }
            
            return []; // Delayed jobs don't return immediate results
        }

        // We can pass the overridden channels to the dispatcher if we want,
        // but NotificationDispatcher's signature only takes priority.
        // For simplicity we will handle channels override here if needed,
        // but the spec just says fluent interface. We will let the dispatcher
        // handle it or we can extend the dispatcher to take channels.
        return $this->dispatcher->dispatch($this->to, $this->notification, $this->priority, $this->channels);
    }

    public function __destruct()
    {
        if (!$this->dispatched) {
            $this->dispatch();
        }
    }
}
