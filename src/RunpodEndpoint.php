<?php

namespace Andmarruda\RunpodModule;

use Andmarruda\RunpodModule\Infrastructure\RunpodApiClient;

final readonly class RunpodEndpoint
{
    public function __construct(private RunpodApiClient $client, private string $endpointId) {}

    public function run(array $input, array $policy = []): array
    {
        return $this->client->runJob($this->endpointId, $input, $policy);
    }

    public function status(string $jobId): array
    {
        return $this->client->getJobStatus($this->endpointId, $jobId);
    }

    public function cancel(string $jobId): array
    {
        return $this->client->cancelJob($this->endpointId, $jobId);
    }

    public function logs(string $jobId): array
    {
        return $this->client->getJobLogs($this->endpointId, $jobId);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->client->get($this->endpointPath($path), $query);
    }

    public function post(string $path, array $payload = []): array
    {
        return $this->client->post($this->endpointPath($path), $payload);
    }

    private function endpointPath(string $path): string
    {
        return trim($this->endpointId, '/').'/'.trim($path, '/');
    }
}
