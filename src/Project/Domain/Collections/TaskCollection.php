<?php

declare(strict_types=1);

namespace Src\Project\Domain\Collections;

use Src\Project\Domain\Task;
use Src\Shared\Domain\Collection\Collection;

/**
 * @extends Collection<Task>
 */
final class TaskCollection extends Collection
{
    protected function type(): string
    {
        return Task::class;
    }
}
