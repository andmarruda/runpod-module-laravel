# Feature - RunPod API SDK Package

## Summary

Provide a Laravel package that centralizes RunPod API access for generic HTTP calls, serverless endpoints, logs and billing. Optional operation persistence exists only for host apps that want a local audit trail.

## MVP Capabilities

- Resolve a public `RunpodApi` service from the Laravel container.
- Execute generic RunPod API `GET`, `POST`, `PUT`, `PATCH`, `DELETE` and arbitrary `request()` calls.
- Scope calls to a serverless endpoint through `RunpodApi::endpoint($endpointId)`.
- Run serverless jobs.
- Read job status.
- Read job logs.
- Cancel jobs.
- Read billing responses through `billing()` / `getBilling()`.
- Optionally persist operation state, input, output, metadata, logs and costs.
- Provide fake/test adapters for host development and package tests.

## Package Consumer Experience

After publication, a Laravel app should be able to install with Composer, publish config, configure RunPod env values, then call RunPod API paths through `RunpodApi`.

If a host app wants local reporting, it can also publish migrations and use the optional operation audit services.

Host apps should implement their own callback or webhook controllers when a workflow needs callbacks, because only the host knows the expected payload shape and business side effects.

## Success Criteria

- Host applications can remove direct RunPod HTTP implementation code after installing the package.
- Generic API calls, serverless endpoint helpers and billing reads are available through one public service.
- Optional audit records are queryable for reporting.
- Package test suite passes in isolation.
- The package can be tagged and submitted to Packagist without requiring app code.
