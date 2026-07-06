<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Contracts\ProviderDispatcher;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderDispatchCommand;
use Andmarruda\RunpodModule\Data\ProviderOperation;

final readonly class DispatchProviderOperation
{
    public function __construct(private ProviderDispatcher $dispatcher, private ProviderOperationRepository $repository, private ProviderOperationNotifier $notifier) {}

    public function execute(ProviderDispatchCommand $command): ProviderOperation
    {
        $existing = $this->repository->findByIdempotencyKey($command->idempotencyKey);
        if ($existing instanceof ProviderOperation) {
            return $existing;
        }
        $operation = ProviderOperation::pending($command->tenantId, $command->userId, $command->service, $command->idempotencyKey, $command->input, $command->context, $command->metadata, $command->operationId);
        $operation->markDispatching();
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.dispatching');
        $operation->applyJob($this->dispatcher->dispatch($command));
        $this->repository->save($operation);
        $this->notifier->notify($operation, 'provider.operation.'.$operation->status->value);

        return $operation;
    }
}
