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
        $model = new TaskModel;
        $model->id = $event->aggregateId();
        $model->project_id = $event->projectId;
        $model->name = $event->name;
        $model->description = $event->description;
        $model->status = $event->status;
        $model->assigned_user_id = $event->assignedUserId;
        $model->save();
    }

    private function projectTaskStatusChanged(TaskStatusChanged $event): void
    {
        /** @var TaskModel|null $model */
        $model = TaskModel::query()->find($event->aggregateId());
        if ($model) {
            $model->status = $event->newStatus;
            $model->save();
        }
    }
}
