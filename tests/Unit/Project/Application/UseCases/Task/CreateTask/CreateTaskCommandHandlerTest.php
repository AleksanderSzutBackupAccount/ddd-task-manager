<?php

declare(strict_types=1);

namespace Tests\Unit\Project\Application\UseCases\Task\CreateTask;

use PHPUnit\Framework\TestCase;
use Src\Project\Application\UseCases\Task\CreateTask\CreateTaskCommand;
use Src\Project\Application\UseCases\Task\CreateTask\CreateTaskCommandHandler;
use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Identity\Domain\ValueObjects\UserId;

final class CreateTaskCommandHandlerTest extends TestCase
{
    public function test_it_should_create_task_with_correct_slug(): void
    {
        $projectRepository = $this->createMock(ProjectRepository::class);
        $taskWriteRepository = $this->createMock(TaskWriteRepository::class);
        $taskReadRepository = $this->createMock(TaskReadRepository::class);

        $projectSlug = new ProjectSlug('ABCD');
        $project = Project::create(ProjectId::generate(), 'Alpha', $projectSlug);

        $projectRepository->expects($this->once())
            ->method('findBySlug')
            ->willReturn($project);

        $taskReadRepository->expects($this->once())
            ->method('countByProject')
            ->willReturn(5);

        $taskWriteRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($task) use ($project) {
                return $task->slug()->value() === 'ABCD-6' &&
                       $task->projectId()->equals($project->id()) &&
                       $task->status()->equals(TaskStatus::toDo());
            }));

        $handler = new CreateTaskCommandHandler(
            $projectRepository,
            $taskWriteRepository,
            $taskReadRepository
        );

        $command = new CreateTaskCommand(
            'ABCD',
            'Test Task',
            'Test Description',
            null
        );

        $handler->handle($command);
    }
}
