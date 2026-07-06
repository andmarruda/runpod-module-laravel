<?php

namespace Andmarruda\RunpodModule\Infrastructure;

use Andmarruda\RunpodModule\Contracts\ProviderCanceller;
use Andmarruda\RunpodModule\Contracts\ProviderCostEstimator;
use Andmarruda\RunpodModule\Contracts\ProviderDispatcher;
use Andmarruda\RunpodModule\Contracts\ProviderJobReader;
use Andmarruda\RunpodModule\Contracts\ProviderLogReader;
use Andmarruda\RunpodModule\Data\ProviderCostBreakdown;
use Andmarruda\RunpodModule\Data\ProviderDispatchCommand;
use Andmarruda\RunpodModule\Data\ProviderJob;
use Andmarruda\RunpodModule\Data\ProviderLogEntry;
use Andmarruda\RunpodModule\Data\ProviderOperation;
use Andmarruda\RunpodModule\Data\ProviderResult;
use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

final class RunpodProviderAdapter implements ProviderCanceller, ProviderCostEstimator, ProviderDispatcher, ProviderJobReader, ProviderLogReader
{
    public function dispatch(ProviderDispatchCommand $command): ProviderJob
    {
        $endpointId = $command->service->endpointId;

        if (! is_string($endpointId) || trim($endpointId) === '') {
            throw new \InvalidArgumentException('RunPod endpoint ID is required.');
        }

        $request = [
            'input' => $command->input,
            'policy' => ['idempotency_key' => $command->idempotencyKey],
        ];

        $webhookUrl = config('runpod-module.webhooks.image_generated_url');

        if (is_string($webhookUrl) && trim($webhookUrl) !== '') {
            $request['webhook'] = $webhookUrl;
        }

        $payload = $this->client()->post($endpointId.'/run', $request)->throw()->json();

        return $this->jobFromPayload(is_array($payload) ? $payload : [], (string) Arr::get($payload, 'id', ''));
    }

    public function read(ProviderOperation $operation): ProviderJob
    {
        $providerJobId = $operation->job?->providerJobId;

        if ($providerJobId === null) {
            return new ProviderJob(
                provider: 'runpod',
                providerJobId: 'missing-provider-job-id',
                status: ProviderJobStatus::Failed,
                error: ['code' => 'provider_job_id_missing'],
            );
        }

        $payload = $this->client()->get($operation->service->endpointId.'/status/'.$providerJobId)->throw()->json();

        return $this->jobFromPayload(is_array($payload) ? $payload : [], $providerJobId);
    }

    public function cancel(ProviderOperation $operation): void
    {
        $providerJobId = $operation->job?->providerJobId;

        if ($providerJobId !== null) {
            $this->client()->post($operation->service->endpointId.'/cancel/'.$providerJobId)->throw();
        }
    }

    public function logs(ProviderOperation $operation): array
    {
        $providerJobId = $operation->job?->providerJobId;

        if ($providerJobId === null) {
            return [];
        }

        $payload = $this->client()->get($operation->service->endpointId.'/logs/'.$providerJobId)->throw()->json();
        $entries = Arr::get(is_array($payload) ? $payload : [], 'logs', $payload);

        if (! is_array($entries)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (mixed $entry): ?ProviderLogEntry => $this->logEntry($entry),
            $entries,
        )));
    }

    public function estimate(ProviderOperation $operation): ProviderCostBreakdown
    {
        $rawUsage = $operation->job?->raw['usage'] ?? $operation->metadata['usage'] ?? [];

        if (is_array($rawUsage) && isset($rawUsage['total_cost'])) {
            return new ProviderCostBreakdown(
                currency: (string) ($rawUsage['currency'] ?? 'USD'),
                source: 'provider_reported',
                confidence: 'high',
                totalCost: (string) $rawUsage['total_cost'],
                billableSeconds: isset($rawUsage['billable_seconds']) ? (float) $rawUsage['billable_seconds'] : null,
                executionSeconds: isset($rawUsage['execution_seconds']) ? (float) $rawUsage['execution_seconds'] : null,
                gpuType: isset($rawUsage['gpu_type']) ? (string) $rawUsage['gpu_type'] : null,
                gpuCount: (int) ($rawUsage['gpu_count'] ?? 1),
                endpointId: $operation->service->endpointId,
                rawUsage: $rawUsage,
            );
        }

        $price = (float) config('runpod-module.flux2_dev.price_per_second', 0);
        $executionSeconds = $operation->job?->executionMs !== null ? $operation->job->executionMs / 1000 : null;
        $billableSeconds = $executionSeconds ?? 0.0;
        $gpuCount = max(1, (int) config('runpod-module.flux2_dev.gpu_count', 1));
        $computeCost = $price * $billableSeconds * $gpuCount;
        $formattedCost = number_format($computeCost, 6, '.', '');
        $endpointPrice = number_format($price, 8, '.', '');

        return new ProviderCostBreakdown(
            currency: 'USD',
            source: 'estimated',
            confidence: $price > 0 && $executionSeconds !== null ? 'medium' : 'low',
            totalCost: $formattedCost,
            computeCost: $formattedCost,
            billableSeconds: $billableSeconds,
            executionSeconds: $executionSeconds,
            gpuType: config('runpod-module.flux2_dev.gpu_type'),
            gpuCount: $gpuCount,
            endpointId: $operation->service->endpointId,
            endpointPricePerSecond: $endpointPrice,
            pricingSnapshot: [
                'provider' => 'runpod',
                'deployment' => $operation->service->deployment,
                'endpoint_id' => $operation->service->endpointId,
                'price_per_second' => $endpointPrice,
                'gpu_type' => config('runpod-module.flux2_dev.gpu_type'),
                'gpu_count' => $gpuCount,
            ],
            rawUsage: is_array($rawUsage) ? $rawUsage : [],
        );
    }

    private function client(): PendingRequest
    {
        $apiKey = config('runpod-module.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            throw new \RuntimeException('RUNPOD_API_KEY is required.');
        }

        return Http::baseUrl(rtrim((string) config('runpod-module.base_url'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('runpod-module.timeout', 900));
    }

    private function jobFromPayload(array $payload, string $fallbackProviderJobId): ProviderJob
    {
        $providerJobId = (string) (
            Arr::get($payload, 'id')
            ?? Arr::get($payload, 'job_id')
            ?? Arr::get($payload, 'provider_job_id')
            ?? $fallbackProviderJobId
        );

        if (trim($providerJobId) === '') {
            throw new \RuntimeException('RunPod response did not include a job ID.');
        }

        $status = $this->statusFromPayload($payload);

        return new ProviderJob(
            provider: 'runpod',
            providerJobId: $providerJobId,
            status: $status,
            result: $status === ProviderJobStatus::Succeeded
                ? ProviderResult::fromArray((array) Arr::get($payload, 'output', []))
                : null,
            startedAt: $this->dateValue(Arr::get($payload, 'started_at')),
            completedAt: $this->dateValue(Arr::get($payload, 'completed_at')),
            queueMs: $this->intValue(Arr::get($payload, 'queue_ms') ?? Arr::get($payload, 'delayTime')),
            executionMs: $this->intValue(Arr::get($payload, 'execution_ms') ?? Arr::get($payload, 'executionTime')),
            error: $this->errorFromPayload($payload),
            raw: $payload,
        );
    }

    private function statusFromPayload(array $payload): ProviderJobStatus
    {
        return match (strtoupper((string) (Arr::get($payload, 'status') ?? 'IN_QUEUE'))) {
            'COMPLETED', 'COMPLETE', 'SUCCESS', 'SUCCEEDED' => ProviderJobStatus::Succeeded,
            'FAILED', 'ERROR' => ProviderJobStatus::Failed,
            'CANCELLED', 'CANCELED' => ProviderJobStatus::Cancelled,
            'TIMED_OUT', 'TIMEOUT' => ProviderJobStatus::TimedOut,
            default => ProviderJobStatus::Running,
        };
    }

    private function errorFromPayload(array $payload): array
    {
        $error = Arr::get($payload, 'error');

        if (is_array($error)) {
            return $error;
        }

        if (is_scalar($error) && trim((string) $error) !== '') {
            return ['message' => (string) $error];
        }

        return [];
    }

    private function logEntry(mixed $entry): ?ProviderLogEntry
    {
        if (is_string($entry) && trim($entry) !== '') {
            return new ProviderLogEntry(now(), 'info', $entry, 'runpod');
        }

        if (! is_array($entry)) {
            return null;
        }

        $message = Arr::get($entry, 'message') ?? Arr::get($entry, 'log') ?? Arr::get($entry, 'text');

        if (! is_scalar($message) || trim((string) $message) === '') {
            return null;
        }

        return new ProviderLogEntry(
            timestamp: $this->dateValue(Arr::get($entry, 'timestamp')) ?? now(),
            level: (string) (Arr::get($entry, 'level') ?? 'info'),
            message: (string) $message,
            source: 'runpod',
            metadata: $entry,
        );
    }

    private function dateValue(mixed $value): ?Carbon
    {
        return is_scalar($value) && trim((string) $value) !== '' ? Carbon::parse((string) $value) : null;
    }

    private function intValue(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
