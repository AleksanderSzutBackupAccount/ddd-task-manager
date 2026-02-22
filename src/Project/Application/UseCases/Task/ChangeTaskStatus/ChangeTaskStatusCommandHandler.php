<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\ChangeTaskStatus;

use Src\Project\Domain\Exceptions\TaskNotFoundException;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class ChangeTaskStatusCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TaskWriteRepository $tasks,
    ) {
    }

    public function __invoke(ChangeTaskStatusCommand $command): void
    {
        $task = $this->tasks->findById(new TaskId($command->taskId));
        if (null === $task) {
            throw new TaskNotFoundException();
        }

        $task->changeStatus(new TaskStatus($command->newStatus));
        $this->tasks->save($task);
    }
}
