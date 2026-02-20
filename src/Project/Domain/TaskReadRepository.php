<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Collections\TaskCollection;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Shared\Domain\Bus\DomainEventStored;

interface TaskReadRepository
{
    public function findAll(ProjectId $projectId): TaskCollection;

    public function findByUserId(UserId $userId): TaskCollection;

    /**
     * @return DomainEventStored[]
     */
    public function getHistory(TaskId $id): array;

    public function countByProject(ProjectId $projectId): int;
}
