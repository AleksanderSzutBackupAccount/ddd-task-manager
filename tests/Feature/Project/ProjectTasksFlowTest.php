<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Src\Project\Application\UseCases\ChangeTaskStatus\ChangeTaskStatusCommand;
use Src\Project\Application\UseCases\CreateProject\CreateProjectCommand;
use Src\Project\Application\UseCases\CreateTask\CreateTaskCommand;
use Src\Project\Application\UseCases\GetTaskHistory\GetTaskHistoryQuery;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Tests\TestCase;

final class ProjectTasksFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_flow_creates_and_updates_task_and_tracks_history(): void
    {
        // given existing user
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        // and a project with slug ABCD
        /** @var CommandHandlerInterface $commandBus */
        $commandBus = $this->app->make(CommandHandlerInterface::class);
        $queryBus = $this->app->make(QueryBusInterface::class);

        $commandBus->handle(new CreateProjectCommand(name: 'Alpha', slug: 'ABCD', userId: $user->id));

        // assign user to project (via repo to keep test minimal)
        /** @var ProjectRepository $projects */
        $projects = $this->app->make(ProjectRepository::class);
        $project = $projects->findBySlug(new ProjectSlug('ABCD'));
        $this->assertNotNull($project);
        $project->assignUser($user->id);
        $projects->save($project);

        // when creating a task assigned to that user
        $commandBus->handle(new CreateTaskCommand(
            projectSlug: 'ABCD',
            name: 'First task',
            description: 'Do something',
            assignedUserId: $user->id
        ));

        // then the task exists with id slug-uuid and assigned to user
        /** @var TaskModel $taskModel */
        $taskModel = TaskModel::query()->where('name', 'First task')->firstOrFail();
        $this->assertMatchesRegularExpression('/^ABCD\-[0-9a-fA-F\-]{36}$/', (string) $taskModel->id);
        $this->assertSame((string) $user->id, $taskModel->assigned_user_id);

        // and when changing status to Done
        $commandBus->handle(new ChangeTaskStatusCommand(
            taskId: (string) $taskModel->id,
            newStatus: TaskStatus::DONE
        ));

        $history = $queryBus->ask(new GetTaskHistoryQuery((string) $taskModel->id));

        $this->assertNotEmpty($history);
        $types = array_map(fn ($e) => $e['type'], $history);
        $this->assertContains('task.created', $types);
        $this->assertContains('task.status_changed', $types);
    }
}
