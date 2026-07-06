<?php

namespace Andmarruda\RunpodModule\Tests\Unit;

use Andmarruda\RunpodModule\Support\RunpodWebhookSignature;
use PHPUnit\Framework\TestCase;

final class RunpodWebhookSignatureTest extends TestCase
{
    public function test_it_validates_hmac_sha256_signature(): void
    {
        $body = '{"status":"completed"}';
        $signature = 'sha256='.hash_hmac('sha256', $body, 'secret');

        $this->assertTrue((new RunpodWebhookSignature)->isValid($body, $signature, 'secret'));
        $this->assertFalse((new RunpodWebhookSignature)->isValid($body, 'sha256=invalid', 'secret'));
    }
}
