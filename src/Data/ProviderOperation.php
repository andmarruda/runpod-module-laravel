<?php

namespace Andmarruda\RunpodModule\Data;

use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use Illuminate\Support\Str;

final class ProviderOperation
{
    public function __construct(
        public readonly string $operationId,
        public readonly int $tenantId,
        public readonly ?int $userId,
        public readonly ProviderService $service,
        public ProviderJobStatus $status,
        public readonly string $idempotencyKey,
        public array $input = [],
        public array $context = [],
        public array $metadata = [],
        public ?ProviderJob $job = null,
        public ?ProviderCostBreakdown $cost = null,
        public array $logs = [],
    ) {
        if (trim($operationId) === '' || $tenantId < 1 || trim($idempotencyKey) === '') {
            throw new \InvalidArgumentException('Operation ID, tenant ID and idempotency key are required.');
        }
    }

    public static function pending(int $tenantId, ?int $userId, ProviderService $service, string $idempotencyKey, array $input = [], array $context = [], array $metadata = [], ?string $operationId = null): self
    {
        return new self($operationId ?? (string) Str::uuid(), $tenantId, $userId, $service, ProviderJobStatus::Pending, $idempotencyKey, $input, $context, $metadata);
    }

    public function markDispatching(): void
    {
        $this->status = ProviderJobStatus::Dispatching;
    }

    public function applyJob(ProviderJob $job): void
    {
        $this->job = $job;
        $this->status = $job->status;
    }

    public function recordLogs(array $logs): void
    {
        $this->logs = $logs;
    }

    public function recordCost(ProviderCostBreakdown $cost): void
    {
        $this->cost = $cost;
    }

    public function markCancelled(): void
    {
        $this->status = ProviderJobStatus::Cancelled;
    }

    public function postUuid(): ?string
    {
        $postId = $this->context['post_id'] ?? $this->context['post_uuid'] ?? null;

        return is_scalar($postId) && trim((string) $postId) !== '' ? (string) $postId : null;
    }

    public function toArray(): array
    {
        return [
            'operation_id' => $this->operationId,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'service' => $this->service->toArray(),
            'status' => $this->status->value,
            'idempotency_key' => $this->idempotencyKey,
            'input' => $this->input,
            'context' => $this->context,
            'metadata' => $this->metadata,
            'job' => $this->job?->toArray(),
            'cost' => $this->cost?->toArray(),
            'logs' => array_map(fn (ProviderLogEntry $entry): array => $entry->toArray(), $this->logs),
        ];
    }
}
