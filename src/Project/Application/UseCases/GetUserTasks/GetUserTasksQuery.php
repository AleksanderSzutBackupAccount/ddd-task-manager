<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetUserTasks;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<array>
 */
final readonly class GetUserTasksQuery implements QueryInterface
{
    public function __construct(public UserId $userId) {}
}
