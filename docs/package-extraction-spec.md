# RunPod Module Laravel - Package Extraction Spec

## Goal

Move RunPod-specific provider operation logic out of Beseenly into a reusable Laravel package. Beseenly must not know RunPod implementation details until the package is published and installed through Composer.

## Boundaries

The package owns:

- provider operation domain data;
- dispatching RunPod jobs;
- reading RunPod job status and result payloads;
- reading provider logs;
- estimating and recording provider costs;
- storing provider operation audit records;
- receiving signed generated-image webhooks;
- emitting Laravel events for host applications.

The host application owns:

- product-specific image generation workflows;
- posts, brands, design agents, Instagram and WordPress logic;
- user/team authorization;
- any UI or websocket broadcasting;
- event listeners that continue business workflows after package events.

## Architecture

The package follows a hexagonal shape:

- `Application`: use cases such as dispatch, refresh, log capture, cost recording and cancellation.
- `Contracts`: ports used by application services.
- `Data` and `Domain`: framework-light DTOs and status enum.
- `Infrastructure`: RunPod adapter, fake adapter and Eloquent repository.
- `Models` and migrations: package-owned persistence for provider audit data.
- `Events`: public integration points for host applications.
- `Http`: webhook controller for provider callbacks.

## Public Contract

Host applications should integrate through:

- application use cases such as `DispatchProviderOperation`;
- contracts such as `ProviderDispatcher`, `ProviderJobReader`, `ProviderLogReader` and `ProviderCostEstimator`;
- events such as `RunpodImageGenerated`, `RunpodImageFailed` and `ProviderOperationUpdated`;
- config values published from `config/runpod-module.php`.

Host applications should not import `RunpodProviderAdapter` directly unless they are extending or testing the package.

## Webhook Contract

Default route:

```text
POST /runpod/webhooks/images/generated
```

Accepted signature headers:

```text
X-Runpod-Signature: sha256=<hmac-sha256-body>
X-Beseenly-Signature: sha256=<hmac-sha256-body>
```

The webhook records provider-reported cost when an operation can be matched by provider job ID, then emits a success or failure event. It does not know how to continue a host-specific design workflow.

## Non-Goals

- Installing the package into Beseenly before Packagist publication.
- Owning Beseenly-specific design agent continuation.
- Owning Instagram or WordPress publishing flows.
- Owning websocket broadcasting.
