<?php

declare(strict_types=1);

namespace Src\Project\Domain\Events;

use Src\Shared\Domain\Bus\DomainEventStored;

final readonly class TaskStatusChanged extends DomainEventStored
{
    public function __construct(
        string $taskId,
        public string $oldStatus,
        public string $newStatus,
        ?string $eventId = null,
        ?\DateTimeImmutable $occurredOn = null,
        int $version = 0,
    ) {
        parent::__construct($taskId, $eventId, $occurredOn, $version);
    }

    public static function eventName(): string
    {
        return 'task.status_changed';
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        \DateTimeImmutable $occurredOn,
        int $version = 0,
    ): self {
        return new self(
            $aggregateId,
            (string) ($body['oldStatus'] ?? ''),
            (string) ($body['newStatus'] ?? ''),
            $eventId,
            $occurredOn,
            $version
        );
    }

    public function toPrimitives(): array
    {
        return [
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
        ];
    }
}
