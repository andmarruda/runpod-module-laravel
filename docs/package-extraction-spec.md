# RunPod Module Laravel - Package Extraction Spec

## Goal

Move RunPod API access into a reusable Laravel package. Host applications should use `RunpodApi` instead of carrying RunPod HTTP details in product workflow code.

## Boundaries

The package owns:

- RunPod API authentication and HTTP request helpers;
- generic `GET`, `POST`, `PUT`, `PATCH`, `DELETE` and arbitrary request calls;
- endpoint-scoped serverless helpers for run, status, logs and cancel;
- billing endpoint reads;
- optional local operation audit persistence;
- optional cost/log recording for reporting.

The host application owns:

- product-specific workflows and payload contracts;
- callback and webhook routes;
- callback replay protection and payload interpretation;
- user/team authorization;
- UI, websocket broadcasting and notifications.

## Architecture

The package follows two layers:

- `RunpodApi`: public SDK-like RunPod API entry point.
- Optional audit layer: application services, models and migrations for storing serverless operation history.

Infrastructure classes such as `RunpodApiClient` and `RunpodProviderAdapter` are implementation details unless a host app is extending the package.

## Public Contract

Host applications should integrate through:

- `RunpodApi` for direct RunPod API reads/writes such as billing, serverless endpoints or custom API paths;
- `RunpodApi::endpoint($endpointId)` for endpoint-scoped run/status/log/cancel helpers;
- optional operation audit services only when local persistence is needed;
- config values published from `config/runpod-module.php`.

## Non-Goals

- Owning host-specific callback or webhook routes.
- Owning product-specific generation, publishing or processing workflows.
- Owning websocket broadcasting.
