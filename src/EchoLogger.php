<?php

namespace ArtARTs36\DocsRetriever;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class EchoLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        fwrite(\STDOUT, sprintf('[%s] %s', $level, $message) . "\n");
    }
}
