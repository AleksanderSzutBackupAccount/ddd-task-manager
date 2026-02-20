<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Bus;

use Illuminate\Support\ServiceProvider;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Application\Bus\DBTransactionCommandHandlerInterface;

class CommandHandlerProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(CommandHandlerInterface::class, CommandHandler::class);
        $this->app->bind(DBTransactionCommandHandlerInterface::class, DBTransactionCommandHandler::class);
    }
}
