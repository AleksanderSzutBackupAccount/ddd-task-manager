<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetAllTasks;

use Src\Project\Domain\Task;
use Src\Project\Domain\TaskReadRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetAllTasksQuery, array<Task>>
 */
final readonly class GetAllTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskReadRepository $tasks) {}

    public function __invoke(QueryInterface $query): array
    {
        return $this->tasks->findAll($query->projectId);
    }
}
