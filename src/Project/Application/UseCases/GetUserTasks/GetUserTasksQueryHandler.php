<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetUserTasks;

use Src\Project\Domain\Task;
use Src\Project\Domain\TaskReadRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetUserTasksQuery, array<Task>>
 */
final readonly class GetUserTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskReadRepository $tasks) {}

    public function __invoke(QueryInterface $query): array
    {
        /** @var GetUserTasksQuery $query */
        return $this->tasks->findByUserId($query->userId);
    }
}
