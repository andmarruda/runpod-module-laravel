# Production Plan

## Phase 1 - API SDK Stabilization

- Keep RunPod API access inside this repository.
- Keep `RunpodApi` as the primary public entry point.
- Keep operation audit services optional.
- Run package checks before tagging: `composer test`, `composer lint:check`, `composer stan`.

## Phase 2 - Packagist Publication

- Add repository metadata and license file.
- Decide the first semantic version tag.
- Publish to Packagist as `andmarruda/runpod-module-laravel`.
- Configure auto-update from GitHub tags if desired.

## Phase 3 - Host Application Integration

- Install the package through Packagist.
- Publish package config and migrations.
- Replace app-local RunPod HTTP clients with `RunpodApi`.
- Use package application services only where local operation audit is needed.
- Keep webhook controllers, replay protection and business continuation in the host application.
- Keep WebSocket broadcasting in the host application, driven by package events or host listeners.

## Phase 4 - Reporting

- Add reports over optional operation audit records, logs and costs.
- Validate cost precision with first real RunPod payloads.
- Store endpoint pricing snapshots when provider pricing changes.
- Add dashboard-ready queries for reporting.

## Phase 5 - Additional Services

- Add additional RunPod serverless endpoint workflows without changing host workflow assumptions.
- Add adapters or service definitions for other RunPod deployments.
- Expand billing/reporting helpers as real API usage requires.
