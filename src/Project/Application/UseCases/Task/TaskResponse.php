<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task;

use Src\Project\Domain\Collections\TaskCollection;
use Src\Project\Domain\Task;

final readonly class TaskResponse
{
    public function __construct(private TaskCollection $tasks) {}

    /**
     * @return array<string, mixed>[]
     */
    public function toResponse(): array
    {
        return $this->tasks->map(static fn (Task $task) => [
            'id' => $task->id()->value(),
            'slug' => $task->slug()->value(),
            'name' => $task->name(),
            'description' => $task->description(),
            'status' => $task->status()->value(),
            'assigned_user_id' => $task->assignedUserId(),
        ]);
    }
}
