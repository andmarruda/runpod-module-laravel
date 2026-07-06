<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderDispatchCommand;
use Andmarruda\RunpodModule\Data\ProviderJob;

interface ProviderDispatcher
{
    public function dispatch(ProviderDispatchCommand $command): ProviderJob;
}
