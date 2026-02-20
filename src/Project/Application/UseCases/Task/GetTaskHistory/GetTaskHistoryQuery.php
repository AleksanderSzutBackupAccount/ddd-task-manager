<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\GetTaskHistory;

use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<array>
 */
final readonly class GetTaskHistoryQuery implements QueryInterface
{
    public function __construct(public string $taskId) {}
}
