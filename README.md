# RunPod Module Laravel

Laravel package for RunPod-backed provider operations.

## Responsibilities

This package owns RunPod-specific behavior:

- dispatching RunPod serverless jobs;
- reading job status/results;
- capturing provider logs;
- estimating or recording cost;
- storing provider operation audit records;
- receiving signed RunPod image completion webhooks;
- emitting Laravel events for host applications.

The host application should not depend on RunPod classes directly. It should listen to package events such as `RunpodImageGenerated` and update its own product models.

## Install Later

Do not install into Beseenly until this package is tagged/published.

```bash
composer require andmarruda/runpod-module-laravel
php artisan vendor:publish --tag=runpod-module-config
php artisan vendor:publish --tag=runpod-module-migrations
php artisan migrate
```

## Events

- `Andmarruda\RunpodModule\Events\ProviderOperationUpdated`
- `Andmarruda\RunpodModule\Events\RunpodImageGenerated`
- `Andmarruda\RunpodModule\Events\RunpodImageFailed`

## Webhook

Default route:

```text
POST /runpod/webhooks/images/generated
```

Signature header:

```text
X-Beseenly-Signature: sha256=<hmac-sha256-body>
```

## Environment

```env
RUNPOD_API_KEY=
RUNPOD_BASE_URL=https://api.runpod.ai/v2
RUNPOD_IMAGE_GENERATED_WEBHOOK_SECRET=
RUNPOD_IMAGE_GENERATED_WEBHOOK_URL=
RUNPOD_FLUX2_DEV_ENDPOINT_ID=
RUNPOD_FLUX2_DEV_PRICE_PER_SECOND=
```

## Documentation

- [Package extraction spec](docs/package-extraction-spec.md)
- [Feature definition](docs/package-feature.md)
- [Production plan](docs/production-plan.md)
- [Tasks](docs/tasks.md)
