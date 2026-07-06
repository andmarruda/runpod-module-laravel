<?php

namespace Andmarruda\RunpodModule\Infrastructure;

use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final class InMemoryProviderOperationRepository implements ProviderOperationRepository
{
    /** @var array<string, ProviderOperation> */
    private array $operations = [];

    public function save(ProviderOperation $operation): void
    {
        $this->operations[$operation->operationId] = $operation;
    }

    public function find(string $operationId): ?ProviderOperation
    {
        return $this->operations[$operationId] ?? null;
    }

    public function findByIdempotencyKey(string $idempotencyKey): ?ProviderOperation
    {
        foreach ($this->operations as $operation) {
            if ($operation->idempotencyKey === $idempotencyKey) {
                return $operation;
            }
        }

        return null;
    }

    public function findByProviderJobId(string $providerJobId): ?ProviderOperation
    {
        foreach ($this->operations as $operation) {
            if ($operation->job?->providerJobId === $providerJobId) {
                return $operation;
            }
        }

        return null;
    }

    public function all(): array
    {
        return array_values($this->operations);
    }
}
