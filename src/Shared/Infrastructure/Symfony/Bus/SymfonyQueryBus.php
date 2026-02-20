<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Bus;

use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SymfonyQueryBus implements QueryBusInterface
{
    private MessageBus $bus;

    public function __construct(iterable $queryHandlers)
    {
        $this->bus = new MessageBus(
            [
                new HandleMessageMiddleware(new HandlersLocator(CallableFirstParameterExtractor::forCallables($queryHandlers))),
            ]
        );
    }

    public function ask(QueryInterface $query): mixed
    {
        try {
            /** @var HandledStamp $stamp */
            $stamp = $this->bus->dispatch($query)->last(HandledStamp::class);

            return $stamp->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
