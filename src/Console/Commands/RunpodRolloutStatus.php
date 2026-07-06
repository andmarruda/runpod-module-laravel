<?php

namespace Andmarruda\RunpodModule\Console\Commands;

use Illuminate\Console\Command;

final class RunpodRolloutStatus extends Command
{
    protected $signature = 'runpod:rollout-status';

    protected $description = 'Report RunPod module configuration';

    public function handle(): int
    {
        $this->table(['Setting', 'Value'], [['driver', (string) config('runpod-module.driver')], ['base_url', (string) config('runpod-module.base_url')], ['api_key', config('runpod-module.api_key') ? 'configured' : 'missing'], ['flux2_endpoint', config('runpod-module.flux2_dev.endpoint_id') ? 'configured' : 'missing'], ['webhook_url', config('runpod-module.webhooks.image_generated_url') ? 'configured' : 'missing']]);

        return self::SUCCESS;
    }
}
