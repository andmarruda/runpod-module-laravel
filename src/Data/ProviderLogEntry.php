<?php

namespace Andmarruda\RunpodModule\Data;

use DateTimeInterface;

final readonly class ProviderLogEntry
{
    public function __construct(
        public DateTimeInterface $timestamp,
        public string $level,
        public string $message,
        public string $source = 'provider',
        public array $metadata = [],
    ) {
        if (trim($message) === '') {
            throw new \InvalidArgumentException('Provider log message is required.');
        }
    }

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->format(DATE_ATOM),
            'level' => $this->level,
            'message' => $this->message,
            'source' => $this->source,
            'metadata' => $this->metadata,
        ];
    }
}
