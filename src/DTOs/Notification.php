<?php

namespace Multek\CustomerEngagement\DTOs;

use DateTimeInterface;

class Notification
{
    public function __construct(
        public readonly string $body,
        public readonly ?string $heading = null,
        public readonly ?string $subtitle = null,
        public readonly ?string $url = null,
        public readonly ?string $imageUrl = null,
        public readonly array $data = [],
        public readonly array $buttons = [],
        public readonly ?string $templateId = null,
        public readonly ?int $priority = null,
        public readonly ?int $ttl = null,
        public readonly DateTimeInterface|string|null $sendAfter = null,
        public readonly ?string $name = null,
    ) {}

    public static function create(string $body): static
    {
        return new static(body: $body);
    }

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'heading' => $this->heading,
            'subtitle' => $this->subtitle,
            'url' => $this->url,
            'image_url' => $this->imageUrl,
            'data' => $this->data,
            'buttons' => $this->buttons,
            'template_id' => $this->templateId,
            'priority' => $this->priority,
            'ttl' => $this->ttl,
            'send_after' => $this->sendAfter instanceof DateTimeInterface
                ? $this->sendAfter->format('c')
                : $this->sendAfter,
            'name' => $this->name,
        ];
    }
}
