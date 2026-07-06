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
use Illuminate\Support\Str;

final class FakeProviderAdapter implements ProviderCanceller, ProviderCostEstimator, ProviderDispatcher, ProviderJobReader, ProviderLogReader
{
    private array $jobs = [];

    public function dispatch(ProviderDispatchCommand $command): ProviderJob
    {
        $job = new ProviderJob($command->service->provider, 'fake-job-'.Str::lower(Str::random(12)), ProviderJobStatus::Running, raw: ['adapter' => 'fake']);
        $this->jobs[$job->providerJobId] = $job;

        return $job;
    }

    public function read(ProviderOperation $operation): ProviderJob
    {
        $providerJobId = $operation->job?->providerJobId;

        return $providerJobId && isset($this->jobs[$providerJobId])
            ? $this->jobs[$providerJobId]
            : new ProviderJob($operation->service->provider, $providerJobId ?? 'fake-missing-job', ProviderJobStatus::Failed, error: ['code' => 'provider_job_not_found']);
    }

    public function logs(ProviderOperation $operation): array
    {
        return [new ProviderLogEntry(now(), 'info', 'Fake provider log.', 'fake')];
    }

    public function estimate(ProviderOperation $operation): ProviderCostBreakdown
    {
        return new ProviderCostBreakdown('USD', 'estimated', 'low', '0.000000', computeCost: '0.000000', billableSeconds: 0.0, endpointId: $operation->service->endpointId, rawUsage: ['adapter' => 'fake']);
    }

    public function cancel(ProviderOperation $operation): void {}

    public function complete(string $providerJobId, ?ProviderResult $result = null): void
    {
        $current = $this->jobs[$providerJobId] ?? null;
        if (! $current instanceof ProviderJob) {
            return;
        }
        $this->jobs[$providerJobId] = new ProviderJob($current->provider, $current->providerJobId, ProviderJobStatus::Succeeded, $result ?? ProviderResult::fromArray(['assets' => []]), completedAt: now(), raw: ['adapter' => 'fake', 'completed' => true]);
    }
}
