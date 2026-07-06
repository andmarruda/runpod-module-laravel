# Tasks

## Package Foundation

- [x] Create Composer package metadata and Laravel auto-discovery provider.
- [x] Add package config and publishable migrations.
- [x] Add provider operation domain/data objects.
- [x] Add application use cases for dispatch, refresh, log capture, cost recording and cancellation.
- [x] Add RunPod adapter behind provider contracts.
- [x] Add fake adapter for package tests and local development.
- [x] Add Eloquent repository and package models.
- [x] Add generated-image webhook controller and signature validation.
- [x] Add package events for host application continuation.
- [x] Add PHPUnit/Testbench test harness.
- [x] Add initial tests for dispatch idempotency and webhook signature handling.
- [x] Add PHPStan and Pint configuration.

## Before First Tag

- [ ] Add `LICENSE` file.
- [ ] Add GitHub Actions for `composer test`, `composer lint:check` and `composer stan`.
- [ ] Add Packagist-ready badges after publication.
- [ ] Review table names and config keys before freezing v1 contract.
- [ ] Validate cost mapping with one real RunPod payload.
- [ ] Add a release checklist to the README.

## After Packagist Publication

- [ ] Install package into Beseenly through Composer.
- [ ] Publish config and migrations in Beseenly.
- [ ] Replace Beseenly-local RunPod implementation with package services.
- [ ] Move designer-agent continuation to a listener for `RunpodImageGenerated`.
- [ ] Keep Beseenly WebSocket/Reverb broadcasting host-owned.
- [ ] Remove duplicated RunPod app-local classes after parity tests pass.
