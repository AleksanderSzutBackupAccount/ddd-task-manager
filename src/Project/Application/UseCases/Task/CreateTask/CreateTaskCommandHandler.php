<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\CreateTask;

use Src\Project\Domain\Exceptions\ProjectNotFoundException;
use Src\Project\Domain\Exceptions\UserNotInProjectException;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\Task;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ProjectRepository $projects,
        private TaskWriteRepository $tasks,
        private TaskReadRepository $taskReadRepository,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $project = $this->projects->findBySlug(new ProjectSlug($command->projectSlug));
        if (null === $project) {
            throw new ProjectNotFoundException();
        }

        if (null !== $command->assignedUserId && !$project->isUserAssigned($command->assignedUserId)) {
            throw new UserNotInProjectException();
        }

        $taskId = TaskId::generate();
        $taskCount = $this->taskReadRepository->countByProject($project->id());
        $taskSlug = new TaskSlug(sprintf('%s-%d', (string) $project->slug(), $taskCount + 1));

        $task = Task::create(
            id: $taskId,
            projectId: $project->id(),
            slug: $taskSlug,
            name: $command->name,
            description: $command->description,
            status: TaskStatus::toDo(),
            assignedUserId: $command->assignedUserId
        );

        $this->tasks->save($task);
    }
}
