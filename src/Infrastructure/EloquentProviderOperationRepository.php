<?php

namespace Andmarruda\RunpodModule\Infrastructure;

use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Data\ProviderCostBreakdown;
use Andmarruda\RunpodModule\Data\ProviderJob;
use Andmarruda\RunpodModule\Data\ProviderLogEntry;
use Andmarruda\RunpodModule\Data\ProviderOperation as DomainProviderOperation;
use Andmarruda\RunpodModule\Data\ProviderResult;
use Andmarruda\RunpodModule\Data\ProviderService;
use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use Andmarruda\RunpodModule\Models\ProviderOperation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class EloquentProviderOperationRepository implements ProviderOperationRepository
{
    public function save(DomainProviderOperation $operation): void
    {
        $model = ProviderOperation::query()->firstOrNew(['uuid' => $operation->operationId]);
        $job = $operation->job;
        $startedAt = $job instanceof ProviderJob ? $job->startedAt : null;
        $completedAt = $job instanceof ProviderJob ? $job->completedAt : null;

        $model->fill([
            'uuid' => $operation->operationId,
            'team_id' => $operation->tenantId,
            'user_id' => $operation->userId,
            'provider' => $operation->service->provider,
            'service_type' => $operation->service->serviceType,
            'deployment' => $operation->service->deployment,
            'endpoint_id' => $operation->service->endpointId,
            'provider_job_id' => $job?->providerJobId,
            'idempotency_key' => $operation->idempotencyKey,
            'status' => $operation->status->value,
            'input' => $operation->input,
            'output' => $job?->result?->toArray(),
            'context' => $operation->context,
            'metadata' => $operation->metadata,
            'raw_request' => ['input' => $operation->input],
            'raw_response' => $job?->raw,
            'error_code' => $job?->error['code'] ?? null,
            'error_message' => $job?->error['message'] ?? null,
            'dispatched_at' => $model->dispatched_at ?? ($operation->status === ProviderJobStatus::Dispatching ? now() : null),
            'started_at' => $startedAt ?? $model->started_at,
            'completed_at' => $completedAt ?? ($operation->status === ProviderJobStatus::Succeeded ? now() : $model->completed_at),
            'failed_at' => $operation->status->isFailure() ? now() : $model->failed_at,
            'cancelled_at' => $operation->status === ProviderJobStatus::Cancelled ? now() : $model->cancelled_at,
        ])->save();

        if ($operation->logs !== []) {
            $model->logs()->delete();

            foreach ($operation->logs as $entry) {
                $model->logs()->create([
                    'uuid' => (string) Str::uuid(),
                    'provider_job_id' => $job?->providerJobId,
                    'timestamp' => $entry->timestamp,
                    'level' => $entry->level,
                    'source' => $entry->source,
                    'message' => $entry->message,
                    'metadata' => $entry->metadata,
                ]);
            }
        }

        if ($operation->cost instanceof ProviderCostBreakdown) {
            $cost = $operation->cost;
            $model->costs()->create([
                'uuid' => (string) Str::uuid(),
                'source' => $cost->source,
                'confidence' => $cost->confidence,
                'currency' => $cost->currency,
                'total_cost' => $cost->totalCost,
                'compute_cost' => $cost->computeCost,
                'network_cost' => $cost->networkCost,
                'storage_cost' => $cost->storageCost,
                'provider_fee' => $cost->providerFee,
                'billable_seconds' => $cost->billableSeconds,
                'queue_seconds' => $cost->queueSeconds,
                'execution_seconds' => $cost->executionSeconds,
                'gpu_type' => $cost->gpuType,
                'gpu_count' => $cost->gpuCount,
                'endpoint_id' => $cost->endpointId,
                'endpoint_price_per_second' => $cost->endpointPricePerSecond,
                'pricing_snapshot' => $cost->pricingSnapshot,
                'raw_usage' => $cost->rawUsage,
            ]);
        }
    }

    public function find(string $operationId): ?DomainProviderOperation
    {
        $model = ProviderOperation::query()->with(['logs', 'costs'])->where('uuid', $operationId)->first();

        return $model instanceof ProviderOperation ? $this->toDomain($model) : null;
    }

    public function findByIdempotencyKey(string $idempotencyKey): ?DomainProviderOperation
    {
        $model = ProviderOperation::query()->with(['logs', 'costs'])->where('idempotency_key', $idempotencyKey)->first();

        return $model instanceof ProviderOperation ? $this->toDomain($model) : null;
    }

    public function findByProviderJobId(string $providerJobId): ?DomainProviderOperation
    {
        $model = ProviderOperation::query()->with(['logs', 'costs'])->where('provider_job_id', $providerJobId)->first();

        return $model instanceof ProviderOperation ? $this->toDomain($model) : null;
    }

    public function all(): array
    {
        return ProviderOperation::query()
            ->with(['logs', 'costs'])
            ->latest('id')
            ->get()
            ->map(fn (ProviderOperation $model): DomainProviderOperation => $this->toDomain($model))
            ->all();
    }

    private function toDomain(ProviderOperation $model): DomainProviderOperation
    {
        $job = $model->provider_job_id ? new ProviderJob(
            provider: $model->provider,
            providerJobId: $model->provider_job_id,
            status: ProviderJobStatus::from($model->status),
            result: is_array($model->output) ? ProviderResult::fromArray($model->output) : null,
            startedAt: $model->started_at,
            completedAt: $model->completed_at,
            error: array_filter(['code' => $model->error_code, 'message' => $model->error_message]),
            raw: $model->raw_response ?? [],
        ) : null;

        $latestCost = $model->costs->sortByDesc('id')->first();
        $cost = $latestCost ? new ProviderCostBreakdown(
            currency: $latestCost->currency,
            source: $latestCost->source,
            confidence: $latestCost->confidence,
            totalCost: (string) $latestCost->total_cost,
            computeCost: $latestCost->compute_cost,
            networkCost: $latestCost->network_cost,
            storageCost: $latestCost->storage_cost,
            providerFee: $latestCost->provider_fee,
            billableSeconds: $latestCost->billable_seconds !== null ? (float) $latestCost->billable_seconds : null,
            queueSeconds: $latestCost->queue_seconds !== null ? (float) $latestCost->queue_seconds : null,
            executionSeconds: $latestCost->execution_seconds !== null ? (float) $latestCost->execution_seconds : null,
            gpuType: $latestCost->gpu_type,
            gpuCount: $latestCost->gpu_count,
            endpointId: $latestCost->endpoint_id,
            endpointPricePerSecond: $latestCost->endpoint_price_per_second,
            pricingSnapshot: $latestCost->pricing_snapshot ?? [],
            rawUsage: $latestCost->raw_usage ?? [],
        ) : null;

        $logs = $model->logs
            ->map(fn ($log): ProviderLogEntry => new ProviderLogEntry(
                timestamp: $log->timestamp ?? Carbon::now(),
                level: $log->level,
                message: $log->message,
                source: $log->source,
                metadata: $log->metadata ?? [],
            ))
            ->values()
            ->all();

        return new DomainProviderOperation(
            operationId: $model->uuid,
            tenantId: $model->team_id,
            userId: $model->user_id,
            service: new ProviderService($model->service_type, $model->provider, $model->deployment, $model->endpoint_id),
            status: ProviderJobStatus::from($model->status),
            idempotencyKey: $model->idempotency_key,
            input: $model->input ?? [],
            context: $model->context ?? [],
            metadata: $model->metadata ?? [],
            job: $job,
            cost: $cost,
            logs: $logs,
        );
    }
}
