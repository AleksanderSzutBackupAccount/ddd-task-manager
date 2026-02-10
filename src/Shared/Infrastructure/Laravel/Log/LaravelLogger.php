<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Log;

use Illuminate\Support\Facades\Log;
use Src\Shared\Application\Log\LoggerInterface;

final readonly class LaravelLogger implements LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        Log::debug($message, $context);
    }
}
