<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateTask;

use Ramsey\Uuid\Uuid;
use Src\Project\Domain\Exceptions\ProjectNotFoundException;
use Src\Project\Domain\Exceptions\UserNotInProjectException;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\Task;
use Src\Project\Domain\TaskRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ProjectRepository $projects,
        private TaskRepository $tasks
    ) {}

    public function handle(CommandInterface $command): void
    {
        /** @var CreateTaskCommand $command */
        $project = $this->projects->findBySlug(new ProjectSlug($command->projectSlug));
        if ($project === null) {
            throw new ProjectNotFoundException;
        }

        if ($command->assignedUserId !== null && ! $project->isUserAssigned($command->assignedUserId)) {
            throw new UserNotInProjectException;
        }

        $uuid = Uuid::uuid4()->toString();
        $taskId = TaskId::fromSlugAndUuid((string) $project->slug(), $uuid);

        $task = Task::create(
            id: $taskId,
            projectId: $project->id(),
            name: $command->name,
            description: $command->description,
            status: TaskStatus::toDo(),
            assignedUserId: $command->assignedUserId
        );

        $this->tasks->save($task);
    }
}
