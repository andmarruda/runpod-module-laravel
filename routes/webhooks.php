<?php

use Andmarruda\RunpodModule\Http\Controllers\RunpodImageGeneratedWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/images/generated', RunpodImageGeneratedWebhookController::class)
    ->name('runpod-module.webhooks.images.generated');
