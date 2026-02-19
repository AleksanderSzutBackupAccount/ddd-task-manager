<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\ValueObjects\TaskId;

interface TaskWriteRepository
{
    public function save(Task $task): void;

    public function findById(TaskId $id): ?Task;
}
