<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Bus;

use Ramsey\Uuid\Uuid;

abstract readonly class DomainEventStored implements DomainEvent
{
    private string $eventId;

    private \DateTimeImmutable $occurredOn;

    public function __construct(
        private string $aggregateId,
        ?string $eventId = null,
        ?\DateTimeImmutable $occurredOn = null,
        private int $version = 0,
    ) {
        $this->eventId = $eventId ?: Uuid::uuid4()->toString();
        $this->occurredOn = $occurredOn ?: new \DateTimeImmutable();
    }

    /**
     * @param array<string, mixed> $body
     */
    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        \DateTimeImmutable $occurredOn,
        int $version = 0,
    ): self;

    abstract public static function eventName(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function toPrimitives(): array;

    final public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    final public function version(): int
    {
        return $this->version;
    }
}
