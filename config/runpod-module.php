<?php

return [
    'driver' => env('RUNPOD_PROVIDER_DRIVER', 'runpod'),
    'base_url' => env('RUNPOD_BASE_URL', 'https://api.runpod.ai/v2'),
    'api_key' => env('RUNPOD_API_KEY'),
    'timeout' => (int) env('RUNPOD_DEFAULT_TIMEOUT', 900),
    'poll_interval' => (int) env('RUNPOD_DEFAULT_POLL_INTERVAL', 5),
    'max_asset_bytes' => (int) env('RUNPOD_MAX_ASSET_BYTES', 20000000),
    'allowed_asset_mime_types' => ['image/png', 'image/jpeg', 'image/webp'],

    'webhooks' => [
        'image_generated_secret' => env('RUNPOD_IMAGE_GENERATED_WEBHOOK_SECRET'),
        'image_generated_url' => env('RUNPOD_IMAGE_GENERATED_WEBHOOK_URL'),
        'route_prefix' => env('RUNPOD_WEBHOOK_ROUTE_PREFIX', 'runpod/webhooks'),
    ],

    'flux2_dev' => [
        'endpoint_id' => env('RUNPOD_FLUX2_DEV_ENDPOINT_ID'),
        'price_per_second' => env('RUNPOD_FLUX2_DEV_PRICE_PER_SECOND'),
        'gpu_type' => env('RUNPOD_FLUX2_DEV_GPU_TYPE'),
        'gpu_count' => (int) env('RUNPOD_FLUX2_DEV_GPU_COUNT', 1),
        'steps' => (int) env('RUNPOD_FLUX2_DEV_STEPS', 28),
        'guidance' => (float) env('RUNPOD_FLUX2_DEV_GUIDANCE', 3.5),
    ],
];
