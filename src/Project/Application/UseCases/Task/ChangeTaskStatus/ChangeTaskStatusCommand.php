<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\ChangeTaskStatus;

use Src\Shared\Domain\Bus\CommandInterface;

final readonly class ChangeTaskStatusCommand implements CommandInterface
{
    public function __construct(
        public string $taskId,
        public string $newStatus
    ) {}
}
