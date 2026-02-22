<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\GetUserTasks;

use Src\Project\Application\UseCases\Task\TaskResponse;
use Src\Project\Domain\TaskReadRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;

/**
 * @implements QueryHandlerInterface<GetUserTasksQuery, TaskResponse>
 */
final readonly class GetUserTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskReadRepository $tasks)
    {
    }

    public function __invoke(GetUserTasksQuery $query): TaskResponse
    {
        return new TaskResponse($this->tasks->findByUserId($query->userId));
    }
}
