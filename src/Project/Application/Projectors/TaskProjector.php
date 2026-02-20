<?php

declare(strict_types=1);

namespace Src\Project\Application\Projectors;

use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\Bus\EventProjector;

final class TaskProjector implements EventProjector
{
    public function __construct(
    ) {}

    public function project(DomainEventStored $event): void
    {
        match (true) {
            $event instanceof TaskCreated => $this->projectTaskCreated($event),
            $event instanceof TaskStatusChanged => $this->projectTaskStatusChanged($event),
            default => null,
        };
    }

    private function projectTaskCreated(TaskCreated $event): void
    {
        TaskModel::query()->create(
            ['id' => $event->aggregateId(),
                'project_id' => $event->projectId,
                'name' => $event->name,
                'description' => $event->description,
                'status' => $event->status,
                'assigned_user_id' => $event->assignedUserId,
            ]
        );
    }

    private function projectTaskStatusChanged(TaskStatusChanged $event): void
    {
        TaskModel::query()->where('id', $event->aggregateId())->update([
            'status' => $event->newStatus,
        ]);
    }
}
