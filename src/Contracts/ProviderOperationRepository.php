<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderOperation;

interface ProviderOperationRepository
{
    public function save(ProviderOperation $operation): void;

    public function find(string $operationId): ?ProviderOperation;

    public function findByIdempotencyKey(string $idempotencyKey): ?ProviderOperation;

    public function findByProviderJobId(string $providerJobId): ?ProviderOperation;

    public function all(): array;
}
