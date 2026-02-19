<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetAllTasks;

use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<array>
 */
final readonly class GetAllTasksQuery implements QueryInterface
{
    public function __construct(public ProjectId $projectId) {}
}
