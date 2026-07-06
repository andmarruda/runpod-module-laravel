<?php

namespace Andmarruda\RunpodModule\Events;

use Andmarruda\RunpodModule\Data\ProviderOperation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ProviderOperationUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly ProviderOperation $operation, public readonly string $event) {}
}
