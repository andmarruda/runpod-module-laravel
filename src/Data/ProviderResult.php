<?php

namespace Andmarruda\RunpodModule\Data;

final readonly class ProviderResult
{
    public function __construct(public array $assets = [], public int|string|null $seed = null, public array $raw = []) {}

    public static function fromArray(array $payload): self
    {
        $assets = $payload['assets'] ?? [];

        return new self(
            assets: is_array($assets) ? array_values(array_filter($assets, 'is_array')) : [],
            seed: $payload['seed'] ?? null,
            raw: $payload,
        );
    }

    public function toArray(): array
    {
        return ['assets' => $this->assets, 'seed' => $this->seed, 'raw' => $this->raw];
    }
}
