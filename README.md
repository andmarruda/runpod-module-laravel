# RunPod Module Laravel

Laravel SDK-style package for RunPod API access.

## Main API

Use `Andmarruda\RunpodModule\RunpodApi` from the Laravel container:

```php
use Andmarruda\RunpodModule\RunpodApi;

$runpod = app(RunpodApi::class);

$runpod->get('billing');
$runpod->post('custom/path', ['key' => 'value']);
$runpod->request('GET', 'custom/search', query: ['page' => 2]);
```

Endpoint-scoped helpers:

```php
$endpoint = $runpod->endpoint('endpoint-id');

$endpoint->run(['task' => 'sync'], ['idempotency_key' => 'job-123']);
$endpoint->status('job-123');
$endpoint->logs('job-123');
$endpoint->cancel('job-123');
```

Billing:

```php
$runpod->billing();
$runpod->billing(['from' => '2026-07-01']);
```

## Optional Audit Layer

The package also includes optional application services and migrations for storing serverless operation history, logs and cost records:

- `DispatchProviderOperation`
- `RefreshProviderOperation`
- `CaptureProviderOperationLogs`
- `RecordProviderOperationCost`
- `CancelProviderOperation`
- `ProviderOperationUpdated`

Use this layer only when a host app wants a local audit trail. Direct RunPod API access should go through `RunpodApi`.

## Host-Owned Concerns

The host application owns product workflows, callbacks, authorization, UI updates, notifications and any webhook payload interpretation.

## Install Later

Do not install into a host application until this package is tagged/published.

```bash
composer require andmarruda/runpod-module-laravel
php artisan vendor:publish --tag=runpod-module-config
php artisan vendor:publish --tag=runpod-module-migrations
php artisan migrate
```

## Environment

```env
RUNPOD_API_KEY=
RUNPOD_BASE_URL=https://api.runpod.ai/v2
RUNPOD_DEFAULT_TIMEOUT=900
RUNPOD_DEFAULT_PRICE_PER_SECOND=
RUNPOD_DEFAULT_GPU_TYPE=
RUNPOD_DEFAULT_GPU_COUNT=1
RUNPOD_BILLING_PATH=billing
```

## Documentation

- [Package extraction spec](docs/package-extraction-spec.md)
- [Feature definition](docs/package-feature.md)
- [Production plan](docs/production-plan.md)
- [Tasks](docs/tasks.md)
