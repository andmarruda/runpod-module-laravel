<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderOperation;

interface ProviderCanceller
{
    public function cancel(ProviderOperation $operation): void;
}
