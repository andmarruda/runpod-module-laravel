<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderJob;
use Andmarruda\RunpodModule\Data\ProviderOperation;

interface ProviderJobReader
{
    public function read(ProviderOperation $operation): ProviderJob;
}
