# RunPod Module Laravel

Laravel SDK-style package for RunPod API access, serverless jobs, billing, logs, and optional operation audit.

Package name:

```bash
andmarruda/runpod-module-laravel
```

## Installation

Install the package in a Laravel application:

```bash
composer require andmarruda/runpod-module-laravel
```

Laravel package auto-discovery registers the service provider automatically.

Publish the config:

```bash
php artisan vendor:publish --tag=runpod-module-config
```

If the application wants the optional local audit tables for operations, logs and costs, publish and run the migrations:

```bash
php artisan vendor:publish --tag=runpod-module-migrations
php artisan migrate
```

## Configuration

Add the RunPod API key to the host application's `.env`:

```env
RUNPOD_API_KEY=
RUNPOD_BASE_URL=https://api.runpod.ai/v2
RUNPOD_DEFAULT_TIMEOUT=900
RUNPOD_DEFAULT_POLL_INTERVAL=5
```

Optional cost estimation settings:

```env
RUNPOD_DEFAULT_PRICE_PER_SECOND=
RUNPOD_DEFAULT_GPU_TYPE=
RUNPOD_DEFAULT_GPU_COUNT=1
```

Optional billing path override:

```env
RUNPOD_BILLING_PATH=billing
```

## Quick Start

Resolve the main SDK object from Laravel's container:

```php
use Andmarruda\RunpodModule\RunpodApi;

$runpod = app(RunpodApi::class);
```

Run a serverless job:

```php
$response = $runpod->runJob(
    endpointId: 'your-endpoint-id',
    input: [
        'task' => 'sync',
        'payload' => ['external_id' => 'abc-123'],
    ],
    policy: [
        'idempotency_key' => 'abc-123:sync',
    ],
);

$jobId = $response['id'] ?? null;
```

Read job status:

```php
$status = $runpod->getJobStatus('your-endpoint-id', $jobId);
```

Read job logs:

```php
$logs = $runpod->getJobLogs('your-endpoint-id', $jobId);
```

Cancel a job:

```php
$runpod->cancelJob('your-endpoint-id', $jobId);
```

## Endpoint Helper

For repeated calls to the same endpoint:

```php
$endpoint = $runpod->endpoint('your-endpoint-id');

$job = $endpoint->run(['task' => 'sync'], ['idempotency_key' => 'abc-123:sync']);
$status = $endpoint->status($job['id']);
$logs = $endpoint->logs($job['id']);
$endpoint->cancel($job['id']);
```

Endpoint-scoped custom API paths are also available:

```php
$endpoint->get('health');
$endpoint->post('custom-action', ['enabled' => true]);
```

## Generic RunPod API Calls

Use generic methods when RunPod adds or changes endpoints and the package does not need a dedicated helper:

```php
$runpod->get('billing');
$runpod->post('custom/path', ['key' => 'value']);
$runpod->put('custom/resource', ['name' => 'Updated']);
$runpod->patch('custom/resource', ['enabled' => true]);
$runpod->delete('custom/resource');
```

For full control over method, payload, query and headers:

```php
$response = $runpod->request(
    method: 'GET',
    path: 'custom/search',
    query: ['page' => 2],
    headers: ['X-Custom-Header' => 'value'],
);
```

## Billing

Read the configured billing endpoint:

```php
$billing = $runpod->billing();
$billing = $runpod->billing(['from' => '2026-07-01']);
```

The billing path is configurable through `RUNPOD_BILLING_PATH`.

## Optional Operation Audit

The package includes an optional persistence layer for applications that want local operation history, logs and cost records.

Application services:

- `Andmarruda\RunpodModule\Application\DispatchProviderOperation`
- `Andmarruda\RunpodModule\Application\RefreshProviderOperation`
- `Andmarruda\RunpodModule\Application\CaptureProviderOperationLogs`
- `Andmarruda\RunpodModule\Application\RecordProviderOperationCost`
- `Andmarruda\RunpodModule\Application\CancelProviderOperation`

Generic event:

- `Andmarruda\RunpodModule\Events\ProviderOperationUpdated`

Use this layer when the host application needs a database-backed audit trail. For direct RunPod API access, prefer `RunpodApi`.

## Host-Owned Concerns

This package does not own product workflows, callback routes, webhook payload contracts, authorization rules, UI updates, notifications, or business-specific continuation logic.

If a RunPod workflow needs callbacks or webhooks, implement that controller in the host application where the expected payload shape and side effects are known.

## Package Development

Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Run formatting check:

```bash
composer lint:check
```

Run static analysis:

```bash
composer stan
```

Validate Composer metadata:

```bash
composer validate --strict
```

## Release Checklist

Before tagging the first release:

- confirm the package name is `andmarruda/runpod-module-laravel`;
- add a `LICENSE` file;
- verify `composer validate --strict`;
- run `composer test`, `composer lint:check`, and `composer stan`;
- validate RunPod API paths against a real API key in a host app or sandbox;
- review the migration table names before freezing the first public version.
