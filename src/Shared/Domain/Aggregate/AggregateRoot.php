<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate;

use Src\Shared\Domain\Bus\DomainEvent;
use Src\Shared\Domain\Bus\DomainEventStored;

abstract class AggregateRoot
{
    /**
     * @var DomainEvent[]
     */
    private array $domainEvents = [];

    private int $version = 0;

    /**
     * @return DomainEvent[]
     */
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    final protected function recordAndApply(DomainEventStored $domainEvent): void
    {
        $this->version++;
        $eventWithVersion = $this->enrichEventWithVersion($domainEvent, $this->version);
        $this->record($eventWithVersion);
        $this->apply($eventWithVersion);
    }

    abstract public function apply(DomainEvent $domainEvent): void;

    final public function version(): int
    {
        return $this->version;
    }

    final protected function setVersion(int $version): void
    {
        $this->version = $version;
    }

    private function enrichEventWithVersion(DomainEventStored $event, int $version): DomainEventStored
    {
        $reflection = new \ReflectionClass($event);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $event;
        }

        $params = $constructor->getParameters();
        $args = [];

        foreach ($params as $param) {
            $name = $param->getName();
            if ($name === 'version') {
                $args[] = $version;
            } elseif ($name === 'id' || $name === 'aggregateId' || $name === 'taskId') {
                $args[] = $event->aggregateId();
            } elseif ($name === 'eventId') {
                $args[] = $event->eventId();
            } elseif ($name === 'occurredOn') {
                $args[] = $event->occurredOn();
            } elseif ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);
                $args[] = $property->getValue($event);
            } else {
                // This is a bit hacky, but usually domain events have properties matching constructor params
                // If not, we might need a more robust way to clone with new version
                $args[] = null;
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}
