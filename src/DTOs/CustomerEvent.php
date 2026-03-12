<?php

namespace Multek\CustomerEngagement\DTOs;

use DateTimeInterface;

class CustomerEvent
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly array $payload = [],
        public readonly ?DateTimeInterface $timestamp = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            externalId: $data['external_id'],
            name: $data['name'],
            payload: $data['payload'] ?? [],
            timestamp: $data['timestamp'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'name' => $this->name,
            'payload' => $this->payload,
            'timestamp' => $this->timestamp?->format('c'),
        ];
    }
}
