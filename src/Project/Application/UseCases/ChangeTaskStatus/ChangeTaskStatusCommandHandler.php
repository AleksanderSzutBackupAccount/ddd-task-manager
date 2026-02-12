<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\ChangeTaskStatus;

use Src\Project\Domain\Exceptions\TaskNotFoundException;
use Src\Project\Domain\TaskRepository;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Domain\Bus\CommandInterface;

final readonly class ChangeTaskStatusCommandHandler implements CommandHandlerInterface
{
    public function __construct(private TaskRepository $tasks) {}

    public function handle(CommandInterface $command): void
    {
        /** @var ChangeTaskStatusCommand $command */
        $task = $this->tasks->findById(new TaskId($command->taskId));
        if ($task === null) {
            throw new TaskNotFoundException;
        }

        $task->changeStatus(new TaskStatus($command->newStatus));
        $this->tasks->save($task);
    }
}
