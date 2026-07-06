<?php

namespace Andmarruda\RunpodModule\Support;

use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderCostBreakdown;
use Andmarruda\RunpodModule\Events\RunpodImageFailed;
use Andmarruda\RunpodModule\Events\RunpodImageGenerated;
use Illuminate\Support\Arr;

final readonly class RunpodImageWebhookHandler
{
    public function __construct(private ProviderOperationRepository $repository) {}

    public function handle(array $payload): array
    {
        $operation = null;
        $providerJobId = $this->stringValue(
            Arr::get($payload, 'provider_job_id')
            ?? Arr::get($payload, 'job_id')
            ?? Arr::get($payload, 'id')
        );

        if ($providerJobId !== null) {
            $operation = $this->repository->findByProviderJobId($providerJobId);
        }

        if ($operation !== null) {
            $cost = Arr::get($payload, 'cost');

            if (is_array($cost) && isset($cost['total_cost'])) {
                $operation->recordCost(new ProviderCostBreakdown(
                    currency: (string) ($cost['currency'] ?? 'USD'),
                    source: (string) ($cost['source'] ?? 'provider_reported'),
                    confidence: (string) ($cost['confidence'] ?? 'high'),
                    totalCost: (string) $cost['total_cost'],
                    rawUsage: $cost,
                ));
            }

            $this->repository->save($operation);
        }

        if ($this->isFailureStatus((string) Arr::get($payload, 'status', 'unknown'))) {
            event(new RunpodImageFailed($payload));

            return ['accepted' => true, 'status' => 'failed'];
        }

        event(new RunpodImageGenerated($payload));

        return ['accepted' => true, 'status' => 'generated'];
    }

    private function isFailureStatus(string $status): bool
    {
        return in_array(strtolower($status), [
            'failed',
            'error',
            'cancelled',
            'canceled',
            'timed_out',
            'timeout',
        ], true);
    }

    private function stringValue(mixed $value): ?string
    {
        return is_scalar($value) && trim((string) $value) !== '' ? (string) $value : null;
    }
}
