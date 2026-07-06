<?php

namespace Andmarruda\RunpodModule\Data;

use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use DateTimeInterface;

final readonly class ProviderJob
{
    public function __construct(
        public string $provider,
        public string $providerJobId,
        public ProviderJobStatus $status,
        public ?ProviderResult $result = null,
        public ?DateTimeInterface $startedAt = null,
        public ?DateTimeInterface $completedAt = null,
        public ?int $queueMs = null,
        public ?int $executionMs = null,
        public array $error = [],
        public array $raw = [],
    ) {
        if (trim($provider) === '' || trim($providerJobId) === '') {
            throw new \InvalidArgumentException('Provider and provider job ID are required.');
        }
    }

    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'provider_job_id' => $this->providerJobId,
            'status' => $this->status->value,
            'result' => $this->result?->toArray(),
            'started_at' => $this->startedAt?->format(DATE_ATOM),
            'completed_at' => $this->completedAt?->format(DATE_ATOM),
            'queue_ms' => $this->queueMs,
            'execution_ms' => $this->executionMs,
            'error' => $this->error,
            'raw' => $this->raw,
        ];
    }
}
