<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Contracts\ProviderCanceller;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final readonly class CancelProviderOperation
{
    public function __construct(
        private ProviderCanceller $canceller,
        private ProviderOperationRepository $repository,
        private ProviderOperationNotifier $notifier,
    ) {}

    public function execute(string $operationId): ?ProviderOperation
    {
        $operation = $this->repository->find($operationId);

        if (! $operation instanceof ProviderOperation) {
            return null;
        }

        $this->canceller->cancel($operation);
        $operation->markCancelled();
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.cancelled');

        return $operation;
    }
}
