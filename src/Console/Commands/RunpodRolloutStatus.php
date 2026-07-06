<?php

namespace Andmarruda\RunpodModule\Console\Commands;

use Illuminate\Console\Command;

final class RunpodRolloutStatus extends Command
{
    protected $signature = 'runpod:rollout-status';

    protected $description = 'Report RunPod module configuration';

    public function handle(): int
    {
        $this->table(['Setting', 'Value'], [['driver', (string) config('runpod-module.driver')], ['base_url', (string) config('runpod-module.base_url')], ['api_key', config('runpod-module.api_key') ? 'configured' : 'missing'], ['billing_path', (string) config('runpod-module.billing.path', 'billing')]]);

        return self::SUCCESS;
    }
}
