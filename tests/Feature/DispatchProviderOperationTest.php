<?php

namespace Andmarruda\RunpodModule\Tests\Feature;

use Andmarruda\RunpodModule\Application\DispatchProviderOperation;
use Andmarruda\RunpodModule\Contracts\ProviderDispatcher;
use Andmarruda\RunpodModule\Data\ProviderDispatchCommand;
use Andmarruda\RunpodModule\Data\ProviderService;
use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use Andmarruda\RunpodModule\Infrastructure\FakeProviderAdapter;
use Andmarruda\RunpodModule\Tests\TestCase;

final class DispatchProviderOperationTest extends TestCase
{
    public function test_it_dispatches_provider_operation_through_fake_adapter(): void
    {
        $this->assertInstanceOf(FakeProviderAdapter::class, $this->app->make(ProviderDispatcher::class));

        $operation = $this->app->make(DispatchProviderOperation::class)->execute($this->command());

        $this->assertSame(ProviderJobStatus::Running, $operation->status);
        $this->assertSame('runpod', $operation->service->provider);
        $this->assertNotNull($operation->job);
        $this->assertStringStartsWith('fake-job-', $operation->job->providerJobId);
    }

    public function test_it_reuses_existing_operation_for_same_idempotency_key(): void
    {
        $dispatcher = $this->app->make(DispatchProviderOperation::class);

        $first = $dispatcher->execute($this->command());
        $second = $dispatcher->execute($this->command());

        $this->assertSame($first->operationId, $second->operationId);
        $this->assertSame($first->job?->providerJobId, $second->job?->providerJobId);
    }

    private function command(): ProviderDispatchCommand
    {
        return new ProviderDispatchCommand(
            tenantId: 10,
            userId: 20,
            service: new ProviderService('image_generation', 'runpod', 'flux-2-dev', 'endpoint-123'),
            idempotencyKey: 'post-123:image:hero',
            input: ['prompt' => 'A person looking at a dashboard.'],
            context: ['post_id' => 'post-123'],
        );
    }
}
