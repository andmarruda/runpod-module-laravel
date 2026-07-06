# Tasks

## Package Foundation

- [x] Create Composer package metadata and Laravel auto-discovery provider.
- [x] Add package config and publishable migrations.
- [x] Add provider operation domain/data objects.
- [x] Add application use cases for dispatch, refresh, log capture, cost recording and cancellation.
- [x] Add generic RunPod API client.
- [x] Add public `RunpodApi` SDK entry point.
- [x] Add endpoint-scoped RunPod helpers.
- [x] Add RunPod adapter behind provider contracts.
- [x] Add fake adapter for package tests and local development.
- [x] Add Eloquent repository and package models.
- [x] Add generic package event for host application observation.
- [x] Add PHPUnit/Testbench test harness.
- [x] Add initial tests for dispatch idempotency, API calls, endpoint helpers and cost status behavior.
- [x] Add PHPStan and Pint configuration.

## Before First Tag

- [ ] Add `LICENSE` file.
- [ ] Add GitHub Actions for `composer test`, `composer lint:check` and `composer stan`.
- [ ] Add Packagist-ready badges after publication.
- [ ] Review table names and config keys before freezing v1 contract.
- [ ] Validate cost mapping with one real RunPod payload.
- [ ] Validate billing path and response mapping against production RunPod API usage.
- [ ] Add a release checklist to the README.

## After Packagist Publication

- [ ] Install package into host applications through Composer.
- [ ] Publish config and migrations in host applications.
- [ ] Replace app-local RunPod HTTP implementation with package services.
- [ ] Keep webhook routes and workflow continuation host-owned.
- [ ] Keep host WebSocket/Reverb broadcasting host-owned.
- [ ] Remove duplicated RunPod app-local classes after parity tests pass.
