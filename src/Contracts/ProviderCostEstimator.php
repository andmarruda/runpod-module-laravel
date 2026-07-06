<?php

namespace Andmarruda\RunpodModule\Contracts;

use Andmarruda\RunpodModule\Data\ProviderCostBreakdown;
use Andmarruda\RunpodModule\Data\ProviderOperation;

interface ProviderCostEstimator
{
    public function estimate(ProviderOperation $operation): ProviderCostBreakdown;
}
