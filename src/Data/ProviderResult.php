<?php

namespace Andmarruda\RunpodModule\Data;

final readonly class ProviderResult
{
    public function __construct(public array $data = [], public array $raw = []) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            data: $payload,
            raw: $payload,
        );
    }

    public function toArray(): array
    {
        return ['data' => $this->data, 'raw' => $this->raw];
    }
}
