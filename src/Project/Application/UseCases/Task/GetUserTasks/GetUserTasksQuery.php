<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\GetUserTasks;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\Task\TaskResponse;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<TaskResponse>
 */
final readonly class GetUserTasksQuery implements QueryInterface
{
    public function __construct(public UserId $userId) {}
}
