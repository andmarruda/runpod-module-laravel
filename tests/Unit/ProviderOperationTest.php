<?php

namespace Andmarruda\RunpodModule\Tests\Unit;

use Andmarruda\RunpodModule\Data\ProviderCostBreakdown;
use Andmarruda\RunpodModule\Data\ProviderOperation;
use Andmarruda\RunpodModule\Data\ProviderService;
use Andmarruda\RunpodModule\Domain\ProviderJobStatus;
use PHPUnit\Framework\TestCase;

final class ProviderOperationTest extends TestCase
{
    public function test_recording_cost_preserves_operation_status(): void
    {
        $operation = ProviderOperation::pending(
            tenantId: 10,
            userId: 20,
            service: new ProviderService('serverless_job', 'runpod', 'generic-worker', 'endpoint-123'),
            idempotencyKey: 'job-123:serverless',
        );
        $operation->status = ProviderJobStatus::Succeeded;

        $operation->recordCost(new ProviderCostBreakdown('USD', 'provider_reported', 'high', '0.100000'));

        $this->assertSame(ProviderJobStatus::Succeeded, $operation->status);
        $this->assertSame('0.100000', $operation->cost?->totalCost);
    }
}
