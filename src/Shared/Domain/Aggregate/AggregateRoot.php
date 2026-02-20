<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate;

use Src\Shared\Domain\Bus\DomainEvent;
use Src\Shared\Domain\Bus\DomainEventStored;

abstract class AggregateRoot
{
    /**
     * @var DomainEventStored[]
     */
    private array $domainEvents = [];

    private int $version = 0;

    /**
     * @return DomainEventStored[]
     */
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEventStored $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    final protected function recordAndApply(DomainEventStored $domainEvent): void
    {
        ++$this->version;
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

        if (null === $constructor) {
            return $event;
        }

        $params = $constructor->getParameters();
        $args = [];

        foreach ($params as $param) {
            $name = $param->getName();
            if ('version' === $name) {
                $args[] = $version;
            } elseif ('id' === $name || 'aggregateId' === $name || 'taskId' === $name) {
                $args[] = $event->aggregateId();
            } elseif ('eventId' === $name) {
                $args[] = $event->eventId();
            } elseif ('occurredOn' === $name) {
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
