<?php

namespace malikad778\NotificationCenter\DTOs;

readonly class BulkDispatchResult
{
    public function __construct(
        public int $successful,
        public int $failed,
        public array $errors = [],
    ) {}
}
