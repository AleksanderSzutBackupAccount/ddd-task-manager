<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Bus;

use Illuminate\Contracts\Bus\Dispatcher;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Domain\Bus\CommandInterface;

class CommandHandler implements CommandHandlerInterface
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(CommandInterface $command): void
    {
        $this->dispatcher->dispatch($command);
    }
}
