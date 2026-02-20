<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Src\Shared\Application\Log\LoggerInterface;

final readonly class PsrLogger implements LoggerInterface
{
    public function __construct(private PsrLoggerInterface $logger)
    {
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
