<?php

namespace Andmarruda\RunpodModule\Support;

final class RunpodWebhookSignature
{
    public function isValid(string $payload, ?string $signature, ?string $secret): bool
    {
        $secret = is_string($secret) ? trim($secret) : '';
        $signature = is_string($signature) ? trim($signature) : '';

        if ($secret === '' || $signature === '') {
            return false;
        }

        if (str_starts_with($signature, 'sha256=')) {
            $signature = substr($signature, 7);
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }
}
