<?php

declare(strict_types=1);

namespace Src\Project\Domain\Events;

use Src\Shared\Domain\Bus\DomainEvent;

final readonly class TaskCreated implements DomainEvent
{
    public function __construct(
        public string $taskId,
        public string $projectId,
        public string $name,
        public string $description,
        public string $status,
        public ?string $assignedUserId,
        public \DateTimeImmutable $occurredOn = new \DateTimeImmutable
    ) {}
}
