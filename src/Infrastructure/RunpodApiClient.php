<?php

namespace Andmarruda\RunpodModule\Infrastructure;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class RunpodApiClient
{
    public function request(string $method, string $path, array $payload = [], array $query = [], array $headers = []): array
    {
        $request = $this->client();

        if ($headers !== []) {
            $request = $request->withHeaders($headers);
        }

        $response = $request->send(strtoupper($method), $this->normalizePath($path), [
            'json' => $payload,
            'query' => $query,
        ])->throw()->json();

        return is_array($response) ? $response : [];
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query);
    }

    public function post(string $path, array $payload = []): array
    {
        return $this->request('POST', $path, $payload);
    }

    public function put(string $path, array $payload = []): array
    {
        return $this->request('PUT', $path, $payload);
    }

    public function patch(string $path, array $payload = []): array
    {
        return $this->request('PATCH', $path, $payload);
    }

    public function delete(string $path, array $payload = []): array
    {
        return $this->request('DELETE', $path, $payload);
    }

    public function runJob(string $endpointId, array $input, array $policy = []): array
    {
        $payload = ['input' => $input];

        if ($policy !== []) {
            $payload['policy'] = $policy;
        }

        return $this->post($this->endpointPath($endpointId, 'run'), $payload);
    }

    public function getJobStatus(string $endpointId, string $providerJobId): array
    {
        return $this->get($this->endpointPath($endpointId, 'status/'.$providerJobId));
    }

    public function cancelJob(string $endpointId, string $providerJobId): array
    {
        return $this->post($this->endpointPath($endpointId, 'cancel/'.$providerJobId));
    }

    public function getJobLogs(string $endpointId, string $providerJobId): array
    {
        return $this->get($this->endpointPath($endpointId, 'logs/'.$providerJobId));
    }

    public function getBilling(array $query = []): array
    {
        return $this->get((string) config('runpod-module.billing.path', 'billing'), $query);
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

    private function endpointPath(string $endpointId, string $suffix): string
    {
        $endpointId = trim($endpointId, '/');

        if ($endpointId === '') {
            throw new \InvalidArgumentException('RunPod endpoint ID is required.');
        }

        return $endpointId.'/'.trim($suffix, '/');
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            throw new \InvalidArgumentException('RunPod API path is required.');
        }

        return ltrim($path, '/');
    }
}
