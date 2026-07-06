<?php

return [
    'driver' => env('RUNPOD_PROVIDER_DRIVER', 'runpod'),
    'base_url' => env('RUNPOD_BASE_URL', 'https://api.runpod.ai/v2'),
    'api_key' => env('RUNPOD_API_KEY'),
    'timeout' => (int) env('RUNPOD_DEFAULT_TIMEOUT', 900),
    'poll_interval' => (int) env('RUNPOD_DEFAULT_POLL_INTERVAL', 5),

    'pricing' => [
        'price_per_second' => env('RUNPOD_DEFAULT_PRICE_PER_SECOND'),
        'gpu_type' => env('RUNPOD_DEFAULT_GPU_TYPE'),
        'gpu_count' => (int) env('RUNPOD_DEFAULT_GPU_COUNT', 1),
    ],

    'billing' => [
        'path' => env('RUNPOD_BILLING_PATH', 'billing'),
    ],
];
