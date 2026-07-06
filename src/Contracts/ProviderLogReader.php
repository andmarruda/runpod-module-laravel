<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderOperation;

interface ProviderLogReader
{
    public function logs(ProviderOperation $operation): array;
}
