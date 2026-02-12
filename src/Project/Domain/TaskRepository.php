<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\ValueObjects\TaskId;

interface TaskRepository
{
    public function save(Task $task): void;

    public function findById(TaskId $id): ?Task;

    /**
     * @return Task[]
     */
    public function findAll(): array;

    /**
     * @return Task[]
     */
    public function findByUserId(string $userId): array;
}
