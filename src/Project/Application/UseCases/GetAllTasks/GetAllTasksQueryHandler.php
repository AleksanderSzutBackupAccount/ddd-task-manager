<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetAllTasks;

use Src\Project\Domain\TaskRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetAllTasksQuery, array<\Src\Project\Domain\Task>>
 */
final readonly class GetAllTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskRepository $tasks) {}

    /**
     * @return array<\Src\Project\Domain\Task>
     */
    public function __invoke(QueryInterface $query): array
    {
        return $this->tasks->findAll();
    }
}
