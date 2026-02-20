<?php

declare(strict_types=1);

namespace Src\Project\Domain\Events;

use Src\Shared\Domain\Bus\DomainEventStored;

final readonly class TaskCreated extends DomainEventStored
{
    public function __construct(
        string $id,
        public string $projectId,
        public string $slug,
        public string $name,
        public string $description,
        public string $status,
        public ?string $assignedUserId,
        ?string $eventId = null,
        ?\DateTimeImmutable $occurredOn = null,
        int $version = 0
    ) {
        parent::__construct($id, $eventId, $occurredOn, $version);
    }

    public static function eventName(): string
    {
        return 'task.created';
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        \DateTimeImmutable $occurredOn,
        int $version = 0
    ): self {
        return new self(
            $aggregateId,
            (string) $body['projectId'],
            (string) $body['slug'],
            (string) $body['name'],
            (string) $body['description'],
            (string) $body['status'],
            isset($body['assignedUserId']) ? (string) $body['assignedUserId'] : null,
            $eventId,
            $occurredOn,
            $version
        );
    }

    public function toPrimitives(): array
    {
        return [
            'projectId' => $this->projectId,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'assignedUserId' => $this->assignedUserId,
        ];
    }
}
