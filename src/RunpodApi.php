<?php

namespace Andmarruda\RunpodModule;

use Andmarruda\RunpodModule\Infrastructure\RunpodApiClient;

final readonly class RunpodApi
{
    public function __construct(private RunpodApiClient $client) {}

    public function request(string $method, string $path, array $payload = [], array $query = [], array $headers = []): array
    {
        return $this->client->request($method, $path, $payload, $query, $headers);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->client->get($path, $query);
    }

    public function post(string $path, array $payload = []): array
    {
        return $this->client->post($path, $payload);
    }

    public function put(string $path, array $payload = []): array
    {
        return $this->client->put($path, $payload);
    }

    public function patch(string $path, array $payload = []): array
    {
        return $this->client->patch($path, $payload);
    }

    public function delete(string $path, array $payload = []): array
    {
        return $this->client->delete($path, $payload);
    }

    public function runJob(string $endpointId, array $input, array $policy = []): array
    {
        return $this->client->runJob($endpointId, $input, $policy);
    }

    public function getJobStatus(string $endpointId, string $providerJobId): array
    {
        return $this->client->getJobStatus($endpointId, $providerJobId);
    }

    public function cancelJob(string $endpointId, string $providerJobId): array
    {
        return $this->client->cancelJob($endpointId, $providerJobId);
    }

    public function getJobLogs(string $endpointId, string $providerJobId): array
    {
        return $this->client->getJobLogs($endpointId, $providerJobId);
    }

    public function getBilling(array $query = []): array
    {
        return $this->client->getBilling($query);
    }

    public function billing(array $query = []): array
    {
        return $this->client->getBilling($query);
    }

    public function endpoint(string $endpointId): RunpodEndpoint
    {
        return new RunpodEndpoint($this->client, $endpointId);
    }
}
