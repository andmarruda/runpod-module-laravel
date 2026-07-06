<?php

namespace Andmarruda\RunpodModule\Data;

final readonly class ProviderDispatchCommand
{
    public function __construct(
        public int $tenantId,
        public ?int $userId,
        public ProviderService $service,
        public string $idempotencyKey,
        public array $input = [],
        public array $context = [],
        public array $metadata = [],
        public ?string $operationId = null,
    ) {}
}
