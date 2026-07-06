<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Contracts\ProviderLogReader;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final readonly class CaptureProviderOperationLogs
{
    public function __construct(
        private ProviderLogReader $reader,
        private ProviderOperationRepository $repository,
        private ProviderOperationNotifier $notifier,
    ) {}

    public function execute(string $operationId): ?ProviderOperation
    {
        $operation = $this->repository->find($operationId);

        if (! $operation instanceof ProviderOperation) {
            return null;
        }

        $operation->recordLogs($this->reader->logs($operation));
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.logs_captured');

        return $operation;
    }
}
