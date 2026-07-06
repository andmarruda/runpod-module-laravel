<?php

namespace Andmarruda\RunpodModule\Tests\Feature;

use Andmarruda\RunpodModule\Events\RunpodImageGenerated;
use Andmarruda\RunpodModule\Tests\TestCase;
use Illuminate\Support\Facades\Event;

final class RunpodImageGeneratedWebhookTest extends TestCase
{
    public function test_it_accepts_signed_image_generated_webhook_and_emits_event(): void
    {
        Event::fake([RunpodImageGenerated::class]);

        $body = json_encode([
            'status' => 'completed',
            'provider_job_id' => 'job-123',
            'output' => ['images' => [['url' => 'https://example.test/image.png']]],
            'context' => ['post_id' => 'post-123'],
        ], JSON_THROW_ON_ERROR);

        $this->postJson('/runpod/webhooks/images/generated', json_decode($body, true, 512, JSON_THROW_ON_ERROR), [
            'X-Runpod-Signature' => 'sha256='.hash_hmac('sha256', $body, 'test-secret'),
        ])->assertAccepted()->assertJson(['accepted' => true, 'status' => 'generated']);

        Event::assertDispatched(RunpodImageGenerated::class);
    }

    public function test_it_rejects_invalid_webhook_signature(): void
    {
        $this->postJson('/runpod/webhooks/images/generated', ['status' => 'completed'], [
            'X-Runpod-Signature' => 'sha256=invalid',
        ])->assertUnauthorized();
    }
}
