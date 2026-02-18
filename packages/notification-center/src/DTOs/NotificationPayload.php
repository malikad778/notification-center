<?php

namespace malikad778\NotificationCenter\DTOs;

class NotificationPayload
{
    // PHP 8.4 Asymmetric Visibility
    public private(set) string $title;
    public private(set) string $body;
    public private(set) array $data;
    public private(set) ?string $actionUrl;
    public private(set) ?string $imageUrl;
    public private(set) ?string $groupKey;
    public private(set) ?string $groupLabel;

    public function __construct(
        string $title,
        string $body,
        array $data = [],
        ?string $actionUrl = null,
        ?string $imageUrl = null,
        ?string $groupKey = null,
        ?string $groupLabel = null,
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->actionUrl = $actionUrl;
        $this->imageUrl = $imageUrl;
        $this->groupKey = $groupKey;
        $this->groupLabel = $groupLabel;
    }
}
