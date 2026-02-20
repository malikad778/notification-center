<?php

namespace malikad778\NotificationCenter\DTOs;

readonly class ChannelResult
{
    public function __construct(
        public string $channel,
        public bool $success,
        public ?string $messageId = null,
        public ?string $error = null,
        public array $metadata = [],
    ) {}
}
