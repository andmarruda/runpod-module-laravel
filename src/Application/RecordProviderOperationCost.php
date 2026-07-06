<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Contracts\ProviderCostEstimator;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final readonly class RecordProviderOperationCost
{
    public function __construct(
        private ProviderCostEstimator $estimator,
        private ProviderOperationRepository $repository,
        private ProviderOperationNotifier $notifier,
    ) {}

    public function execute(string $operationId): ?ProviderOperation
    {
        $operation = $this->repository->find($operationId);

        if (! $operation instanceof ProviderOperation) {
            return null;
        }

        $operation->recordCost($this->estimator->estimate($operation));
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.cost_recorded');

        return $operation;
    }
}
