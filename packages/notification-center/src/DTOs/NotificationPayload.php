<?php

namespace malikad778\NotificationCenter\DTOs;

readonly class NotificationPayload
{
    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
        public ?string $actionUrl = null,
        public ?string $imageUrl = null,
        public ?string $groupKey = null,
        public ?string $groupLabel = null,
    ) {}
}
