<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Shared\Domain\Bus\DomainEventStored;

interface TaskReadRepository
{
    /**
     * @return Task[]
     */
    public function findAll(ProjectId $projectId): array;

    /**
     * @return Task[]
     */
    public function findByUserId(UserId $userId): array;

    /**
     * @return DomainEventStored[]
     */
    public function getHistory(TaskId $id): array;
}
