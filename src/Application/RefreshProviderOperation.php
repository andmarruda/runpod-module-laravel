<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Contracts\ProviderJobReader;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final readonly class RefreshProviderOperation
{
    public function __construct(private ProviderJobReader $reader, private ProviderOperationRepository $repository, private ProviderOperationNotifier $notifier) {}

    public function execute(string $operationId): ?ProviderOperation
    {
        $operation = $this->repository->find($operationId);
        if (! $operation instanceof ProviderOperation) {
            return null;
        }
        $operation->applyJob($this->reader->read($operation));
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.'.$operation->status->value);

        return $operation;
    }
}
