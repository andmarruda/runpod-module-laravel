<?php

namespace Andmarruda\RunpodModule\Tests\Feature;

use Andmarruda\RunpodModule\Infrastructure\RunpodApiClient;
use Andmarruda\RunpodModule\RunpodApi;
use Andmarruda\RunpodModule\Tests\TestCase;
use Illuminate\Support\Facades\Http;

final class RunpodApiClientTest extends TestCase
{
    public function test_it_posts_runpod_api_requests(): void
    {
        $this->app['config']->set('runpod-module.api_key', 'test-key');
        Http::fake([
            'https://api.runpod.ai/v2/endpoint-123/run' => Http::response(['id' => 'job-123', 'status' => 'IN_QUEUE']),
        ]);

        $payload = $this->app->make(RunpodApiClient::class)->runJob('endpoint-123', ['task' => 'health-check'], ['idempotency_key' => 'job-123']);

        $this->assertSame('job-123', $payload['id']);
        Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer test-key')
            && $request->url() === 'https://api.runpod.ai/v2/endpoint-123/run'
            && $request['input'] === ['task' => 'health-check']);
    }

    public function test_it_reads_billing_endpoint(): void
    {
        $this->app['config']->set('runpod-module.api_key', 'test-key');
        Http::fake([
            'https://api.runpod.ai/v2/billing*' => Http::response(['balance' => '10.00']),
        ]);

        $payload = $this->app->make(RunpodApiClient::class)->getBilling(['from' => '2026-07-01']);

        $this->assertSame('10.00', $payload['balance']);
        Http::assertSent(fn ($request): bool => $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://api.runpod.ai/v2/billing'));
    }

    public function test_it_exposes_generic_runpod_api_methods(): void
    {
        $this->app['config']->set('runpod-module.api_key', 'test-key');
        Http::fake([
            'https://api.runpod.ai/v2/custom/resource' => Http::response(['ok' => true]),
        ]);

        $payload = $this->app->make(RunpodApi::class)->patch('custom/resource', ['enabled' => true]);

        $this->assertTrue($payload['ok']);
        Http::assertSent(fn ($request): bool => $request->method() === 'PATCH'
            && $request->url() === 'https://api.runpod.ai/v2/custom/resource'
            && $request['enabled'] === true);
    }

    public function test_it_exposes_endpoint_scoped_helpers(): void
    {
        $this->app['config']->set('runpod-module.api_key', 'test-key');
        Http::fake([
            'https://api.runpod.ai/v2/endpoint-123/status/job-123' => Http::response(['id' => 'job-123', 'status' => 'COMPLETED']),
        ]);

        $payload = $this->app->make(RunpodApi::class)->endpoint('endpoint-123')->status('job-123');

        $this->assertSame('COMPLETED', $payload['status']);
        Http::assertSent(fn ($request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.runpod.ai/v2/endpoint-123/status/job-123');
    }

    public function test_it_exposes_low_level_request_method(): void
    {
        $this->app['config']->set('runpod-module.api_key', 'test-key');
        Http::fake([
            'https://api.runpod.ai/v2/custom/search*' => Http::response(['items' => [['id' => 'item-1']]]),
        ]);

        $payload = $this->app->make(RunpodApi::class)->request('GET', 'custom/search', query: ['page' => 2]);

        $this->assertSame('item-1', $payload['items'][0]['id']);
        Http::assertSent(fn ($request): bool => $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://api.runpod.ai/v2/custom/search'));
    }
}
