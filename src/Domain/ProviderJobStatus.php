<?php

namespace Andmarruda\RunpodModule\Domain;

enum ProviderJobStatus: string
{
    case Pending = 'pending';
    case Dispatching = 'dispatching';
    case Running = 'running';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case TimedOut = 'timed_out';

    public function isTerminal(): bool
    {
        return in_array($this, [self::Succeeded, self::Failed, self::Cancelled, self::TimedOut], true);
    }

    public function isFailure(): bool
    {
        return in_array($this, [self::Failed, self::Cancelled, self::TimedOut], true);
    }
}
