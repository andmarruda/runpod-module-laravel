<?php

namespace Andmarruda\RunpodModule\Data;

final readonly class ProviderCostBreakdown
{
    public function __construct(
        public string $currency,
        public string $source,
        public string $confidence,
        public string $totalCost,
        public ?string $computeCost = null,
        public ?string $networkCost = null,
        public ?string $storageCost = null,
        public ?string $providerFee = null,
        public ?float $billableSeconds = null,
        public ?float $queueSeconds = null,
        public ?float $executionSeconds = null,
        public ?string $gpuType = null,
        public int $gpuCount = 1,
        public ?string $endpointId = null,
        public ?string $endpointPricePerSecond = null,
        public array $pricingSnapshot = [],
        public array $rawUsage = [],
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
