<?php

namespace Andmarruda\RunpodModule\Application;

use Andmarruda\RunpodModule\Data\ProviderOperation;
use Andmarruda\RunpodModule\Events\ProviderOperationUpdated;

final class ProviderOperationNotifier
{
    public function notify(ProviderOperation $operation, string $event): void
    {
        event(new ProviderOperationUpdated($operation, $event));
    }
}
