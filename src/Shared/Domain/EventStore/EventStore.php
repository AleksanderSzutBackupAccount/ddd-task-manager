<?php

declare(strict_types=1);

namespace Src\Shared\Domain\EventStore;

use Src\Shared\Domain\Bus\DomainEventStored;

interface EventStore
{
    /**
     * @param DomainEventStored[] $events
     */
    public function append(array $events): void;

    /**
     * @return DomainEventStored[]
     */
    public function getEventsFor(string $aggregateId): array;
}
