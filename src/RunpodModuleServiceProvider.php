<?php

namespace Andmarruda\RunpodModule;

use Andmarruda\RunpodModule\Console\Commands\RunpodRolloutStatus;
use Andmarruda\RunpodModule\Contracts\ProviderCanceller;
use Andmarruda\RunpodModule\Contracts\ProviderCostEstimator;
use Andmarruda\RunpodModule\Contracts\ProviderDispatcher;
use Andmarruda\RunpodModule\Contracts\ProviderJobReader;
use Andmarruda\RunpodModule\Contracts\ProviderLogReader;
use Andmarruda\RunpodModule\Contracts\ProviderOperationRepository;
use Andmarruda\RunpodModule\Infrastructure\EloquentProviderOperationRepository;
use Andmarruda\RunpodModule\Infrastructure\FakeProviderAdapter;
use Andmarruda\RunpodModule\Infrastructure\InMemoryProviderOperationRepository;
use Andmarruda\RunpodModule\Infrastructure\RunpodProviderAdapter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class RunpodModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/runpod-module.php', 'runpod-module');
        $this->app->singleton(FakeProviderAdapter::class);
        $this->app->singleton(RunpodProviderAdapter::class);
        $repositoryClass = $this->app->environment('testing') ? InMemoryProviderOperationRepository::class : EloquentProviderOperationRepository::class;
        $adapterClass = $this->app->environment('testing') || config('runpod-module.driver') === 'fake' ? FakeProviderAdapter::class : RunpodProviderAdapter::class;
        $this->app->singleton(ProviderOperationRepository::class, $repositoryClass);
        foreach ([ProviderDispatcher::class, ProviderJobReader::class, ProviderLogReader::class, ProviderCostEstimator::class, ProviderCanceller::class] as $contract) {
            $this->app->bind($contract, fn () => $this->app->make($adapterClass));
        }
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/runpod-module.php' => config_path('runpod-module.php')], 'runpod-module-config');
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'runpod-module-migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Route::prefix((string) config('runpod-module.webhooks.route_prefix', 'runpod/webhooks'))->middleware('api')->group(__DIR__.'/../routes/webhooks.php');
        if ($this->app->runningInConsole()) {
            $this->commands([RunpodRolloutStatus::class]);
        }
    }
}
