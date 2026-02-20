<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\GetAllTasks;

use Src\Project\Application\UseCases\Task\TaskResponse;
use Src\Project\Domain\TaskReadRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetAllTasksQuery, TaskResponse>
 */
final readonly class GetAllTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskReadRepository $tasks) {}

    public function __invoke(QueryInterface $query): TaskResponse
    {
        return new TaskResponse($this->tasks->findAll($query->projectId));
    }
}
