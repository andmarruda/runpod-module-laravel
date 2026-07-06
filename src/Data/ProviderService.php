<?php

namespace Andmarruda\RunpodModule\Data;

final readonly class ProviderService
{
    public function __construct(
        public string $serviceType,
        public string $provider,
        public string $deployment,
        public ?string $endpointId = null,
    ) {
        foreach (['serviceType' => $serviceType, 'provider' => $provider, 'deployment' => $deployment] as $name => $value) {
            if (trim($value) === '') {
                throw new \InvalidArgumentException("{$name} is required.");
            }
        }
    }

    public function toArray(): array
    {
        return [
            'service_type' => $this->serviceType,
            'provider' => $this->provider,
            'deployment' => $this->deployment,
            'endpoint_id' => $this->endpointId,
        ];
    }
}
