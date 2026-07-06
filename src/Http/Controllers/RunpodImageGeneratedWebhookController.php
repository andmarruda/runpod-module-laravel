<?php

namespace Andmarruda\RunpodModule\Http\Controllers;

use Andmarruda\RunpodModule\Support\RunpodImageWebhookHandler;
use Andmarruda\RunpodModule\Support\RunpodWebhookSignature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class RunpodImageGeneratedWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        RunpodWebhookSignature $signature,
        RunpodImageWebhookHandler $handler,
    ): JsonResponse {
        $isValid = $signature->isValid(
            $request->getContent(),
            $request->header('X-Beseenly-Signature') ?? $request->header('X-Runpod-Signature'),
            config('runpod-module.webhooks.image_generated_secret'),
        );

        if (! $isValid) {
            abort(401, 'Invalid webhook signature.');
        }

        $payload = $request->validate([
            'status' => ['required', 'string'],
            'provider_job_id' => ['nullable', 'string'],
            'job_id' => ['nullable', 'string'],
            'id' => ['nullable', 'string'],
            'context' => ['nullable', 'array'],
            'output' => ['nullable', 'array'],
            'cost' => ['nullable'],
            'usage' => ['nullable', 'array'],
            'logs' => ['nullable', 'array'],
            'error' => ['nullable'],
        ]);

        return response()->json($handler->handle($payload), 202);
    }
}
