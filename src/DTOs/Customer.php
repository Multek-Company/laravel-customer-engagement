<?php

namespace Multek\CustomerEngagement\DTOs;

class Customer
{
    public function __construct(
        public readonly string $externalId,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $name = null,
        public readonly array $attributes = [],
        public readonly array $segments = [],
        public readonly ?string $idempotencyId = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            externalId: $data['external_id'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            name: $data['name'] ?? null,
            attributes: $data['attributes'] ?? [],
            segments: $data['segments'] ?? [],
            idempotencyId: $data['idempotency_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->name,
            'attributes' => $this->attributes,
            'segments' => $this->segments,
            'idempotency_id' => $this->idempotencyId,
        ];
    }

    public function with(array $overrides): static
    {
        return new static(
            externalId: $overrides['external_id'] ?? $this->externalId,
            email: array_key_exists('email', $overrides) ? $overrides['email'] : $this->email,
            phone: array_key_exists('phone', $overrides) ? $overrides['phone'] : $this->phone,
            name: array_key_exists('name', $overrides) ? $overrides['name'] : $this->name,
            attributes: $overrides['attributes'] ?? $this->attributes,
            segments: $overrides['segments'] ?? $this->segments,
            idempotencyId: array_key_exists('idempotency_id', $overrides) ? $overrides['idempotency_id'] : $this->idempotencyId,
        );
    }
}
