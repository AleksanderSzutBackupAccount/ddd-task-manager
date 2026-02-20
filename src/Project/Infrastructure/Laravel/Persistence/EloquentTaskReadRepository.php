<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Collections\TaskCollection;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\EventStore\EventStore;

final readonly class EloquentTaskReadRepository implements TaskReadRepository
{
    public function __construct(private EventStore $eventStore) {}

    public function findAll(ProjectId $projectId): TaskCollection
    {
        $query = TaskModel::query();

        $query->where('project_id', $projectId->value);

        return new TaskCollection($query
            ->get()
            ->map(fn (TaskModel $m) => $m->toEntity())
            ->all());
    }

    public function findByUserId(UserId $userId): TaskCollection
    {
        // only tasks from project where user is a member and assigned to the user
        $projectIds = ProjectModel::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $userId->value()))
            ->pluck('id');

        return new TaskCollection(TaskModel::query()
            ->whereIn('project_id', $projectIds)
            ->where('assigned_user_id', $userId)
            ->get()
            ->map(fn (TaskModel $m) => $m->toEntity())
            ->all());
    }

    public function getHistory(TaskId $id): array
    {
        $events = $this->eventStore->getEventsFor((string) $id);

        usort($events, static fn (DomainEventStored $a, DomainEventStored $b) => $a->version() <=> $b->version());

        return $events;
    }

    public function countByProject(ProjectId $projectId): int
    {
        return TaskModel::query()->where('project_id', $projectId->value)->count();
    }
}
