<?php

namespace Andmarruda\RunpodModule\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class RunpodImageGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly array $payload) {}
}
