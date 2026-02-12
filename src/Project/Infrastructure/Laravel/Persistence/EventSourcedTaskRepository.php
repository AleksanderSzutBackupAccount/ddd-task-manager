<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Project\Domain\Task;
use Src\Project\Domain\TaskRepository;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;
use Src\Project\Infrastructure\Laravel\Models\TaskEventModel;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;

final class EventSourcedTaskRepository implements TaskRepository
{
    public function save(Task $task): void
    {
        $events = $task->pullDomainEvents();

        DB::transaction(function () use ($task, $events): void {
            foreach ($events as $event) {
                $this->appendEvent($event);
            }

            // projection update
            /** @var TaskModel|null $model */
            $model = TaskModel::query()->find((string) $task->id());
            if ($model === null) {
                $model = new TaskModel();
                $model->id = (string) $task->id();
            }
            $model->project_id = (string) $task->projectId();
            $model->name = $task->name();
            $model->description = $task->description();
            $model->status = (string) $task->status();
            $model->assigned_user_id = $task->assignedUserId();
            $model->save();
        });
    }

    public function findById(TaskId $id): ?Task
    {
        /** @var TaskEventModel[] $rows */
        $rows = TaskEventModel::query()->where('task_id', (string) $id)->orderBy('occurred_on')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $events = [];
        foreach ($rows as $row) {
            $occurred = $row->occurred_on instanceof \DateTimeImmutable ? $row->occurred_on : new \DateTimeImmutable((string) $row->occurred_on);
            $events[] = $this->mapEvent($row->event_type, $row->payload, $occurred);
        }

        return Task::reconstitute($events);
    }

    public function findAll(): array
    {
        return TaskModel::query()->get()->map(fn (TaskModel $m) => $m->toEntity())->all();
    }

    public function findByUserId(string $userId): array
    {
        // only tasks from projects where user is a member and assigned to the user
        $projectIds = ProjectModel::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $userId))
            ->pluck('id');

        return TaskModel::query()
            ->whereIn('project_id', $projectIds)
            ->where('assigned_user_id', $userId)
            ->get()
            ->map(fn (TaskModel $m) => $m->toEntity())
            ->all();
    }

    private function appendEvent(object $event): void
    {
        $type = match (true) {
            $event instanceof TaskCreated => 'task.created',
            $event instanceof TaskStatusChanged => 'task.status_changed',
            default => throw new \InvalidArgumentException('Unknown event '.get_class($event)),
        };

        $payload = match ($type) {
            'task.created' => [
                'taskId' => $event->taskId,
                'projectId' => $event->projectId,
                'name' => $event->name,
                'description' => $event->description,
                'status' => $event->status,
                'assignedUserId' => $event->assignedUserId,
            ],
            'task.status_changed' => [
                'taskId' => $event->taskId,
                'oldStatus' => $event->oldStatus,
                'newStatus' => $event->newStatus,
            ],
        };

        TaskEventModel::query()->create([
            'id' => Uuid::uuid4()->toString(),
            'task_id' => $payload['taskId'],
            'event_type' => $type,
            'payload' => $payload,
            'occurred_on' => $event->occurredOn ?? now(),
        ]);
    }

    private function mapEvent(string $type, array $payload, \DateTimeImmutable $occurredOn): object
    {
        return match ($type) {
            'task.created' => new TaskCreated(
                $payload['taskId'],
                $payload['projectId'],
                $payload['name'],
                $payload['description'],
                $payload['status'],
                $payload['assignedUserId'] ?? null,
                $occurredOn
            ),
            'task.status_changed' => new TaskStatusChanged(
                $payload['taskId'],
                $payload['oldStatus'],
                $payload['newStatus'],
                $occurredOn
            ),
            default => throw new \InvalidArgumentException('Unknown type '.$type),
        };
    }
}
