<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Models\NotificationTemplate;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Str;

class NotificationTemplateService
{
    public function __construct(
        protected NotificationDispatcher $dispatcher
    ) {}

    public function send(Notifiable $user, string $templateName, array $data = []): void
    {
        $template = NotificationTemplate::where('name', $templateName)->firstOrFail();

        $title = $this->replacePlaceholders($template->subject, $data);
        $body = $this->replacePlaceholders($template->body, $data);

        $payload = new NotificationPayload(
            title: $title,
            body: $body,
            data: array_merge($template->metadata ?? [], $data),
            actionUrl: $data['actionUrl'] ?? null,
            imageUrl: $data['imageUrl'] ?? null
        );

        $this->dispatcher->dispatch($user, $payload);
    }

    protected function replacePlaceholders(string $text, array $data): string
    {
        // Simple mustache-style replacement {{ var }}
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $text = str_replace("{{ $key }}", (string) $value, $text);
                $text = str_replace("{{$key}}", (string) $value, $text);
            }
        }
        return $text;
    }
}
