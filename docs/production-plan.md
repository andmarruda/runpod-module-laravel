# Production Plan

## Phase 1 - Package Stabilization

- Keep all RunPod implementation inside this repository.
- Maintain Beseenly integration as a later Composer install, not a path install in the app repository.
- Keep the public API focused on Laravel container services, contracts, events and published config.
- Run package checks before tagging: `composer test`, `composer lint:check`, `composer stan`.

## Phase 2 - Packagist Publication

- Add repository metadata and license file.
- Decide the first semantic version tag.
- Publish to Packagist as `andmarruda/runpod-module-laravel`.
- Configure auto-update from GitHub tags if desired.

## Phase 3 - Beseenly Integration

- Install the package in Beseenly through Packagist.
- Publish package config and migrations.
- Replace app-local RunPod classes with package application services and events.
- Move Beseenly-specific continuation into event listeners, for example designer agent continuation after `RunpodImageGenerated`.
- Keep Reverb/WebSocket broadcasting in Beseenly, driven by package events or host listeners.

## Phase 4 - Provider Reporting

- Add reports over provider operations, logs and costs.
- Validate cost precision with first real RunPod payloads.
- Store endpoint pricing snapshots when provider pricing changes.
- Add dashboard-ready queries for MVP partnership reporting.

## Phase 5 - Additional Services

- Add non-image provider operation types without changing websocket or host workflow assumptions.
- Add adapters or service definitions for other RunPod deployments.
- Extend webhook handlers only through generic provider operation concepts.
