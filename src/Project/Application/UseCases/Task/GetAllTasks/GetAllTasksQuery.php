<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\GetAllTasks;

use Src\Project\Application\UseCases\Task\TaskResponse;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<TaskResponse>
 */
final readonly class GetAllTasksQuery implements QueryInterface
{
    public function __construct(public ProjectId $projectId)
    {
    }
}
