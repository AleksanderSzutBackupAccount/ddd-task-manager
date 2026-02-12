<?php

declare(strict_types=1);

namespace Src\Project\Domain\Events;

use Src\Shared\Domain\Bus\DomainEvent;

final readonly class TaskStatusChanged implements DomainEvent
{
    public function __construct(
        public string $taskId,
        public string $oldStatus,
        public string $newStatus,
        public \DateTimeImmutable $occurredOn = new \DateTimeImmutable
    ) {}
}
