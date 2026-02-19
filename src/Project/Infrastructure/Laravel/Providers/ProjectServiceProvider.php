<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Providers;

use Src\Project\Application\Projectors\TaskProjector;
use Src\Project\Application\UseCases\ChangeTaskStatus\ChangeTaskStatusCommand;
use Src\Project\Application\UseCases\ChangeTaskStatus\ChangeTaskStatusCommandHandler;
use Src\Project\Application\UseCases\CreateProject\CreateProjectCommand;
use Src\Project\Application\UseCases\CreateProject\CreateProjectCommandHandler;
use Src\Project\Application\UseCases\CreateTask\CreateTaskCommand;
use Src\Project\Application\UseCases\CreateTask\CreateTaskCommandHandler;
use Src\Project\Application\UseCases\GetAllTasks\GetAllTasksQuery;
use Src\Project\Application\UseCases\GetAllTasks\GetAllTasksQueryHandler;
use Src\Project\Application\UseCases\GetTaskHistory\GetTaskHistoryQuery;
use Src\Project\Application\UseCases\GetTaskHistory\GetTaskHistoryQueryHandler;
use Src\Project\Application\UseCases\GetUserTasks\GetUserTasksQuery;
use Src\Project\Application\UseCases\GetUserTasks\GetUserTasksQueryHandler;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Infrastructure\Laravel\Persistence\EloquentProjectRepository;
use Src\Project\Infrastructure\Laravel\Persistence\EloquentTaskReadRepository;
use Src\Project\Infrastructure\Laravel\Persistence\EventSourcedTaskWriteRepository;
use Src\Shared\Domain\Bus\EventProjector;
use Src\Shared\Domain\EventStore\EventStore;
use Src\Shared\Infrastructure\Laravel\Persistence\EloquentEventStore;
use Src\Shared\Infrastructure\Laravel\Providers\BaseContextServiceProvider;

final class ProjectServiceProvider extends BaseContextServiceProvider
{
    protected array $binds = [
        ProjectRepository::class => EloquentProjectRepository::class,
        TaskReadRepository::class => EloquentTaskReadRepository::class,
        TaskWriteRepository::class => EventSourcedTaskWriteRepository::class,
        EventStore::class => EloquentEventStore::class,
        EventProjector::class => TaskProjector::class,
    ];

    protected array $useCases = [
        CreateProjectCommand::class => CreateProjectCommandHandler::class,
        CreateTaskCommand::class => CreateTaskCommandHandler::class,
        ChangeTaskStatusCommand::class => ChangeTaskStatusCommandHandler::class,
        GetUserTasksQuery::class => GetUserTasksQueryHandler::class,
        GetAllTasksQuery::class => GetAllTasksQueryHandler::class,
        GetTaskHistoryQuery::class => GetTaskHistoryQueryHandler::class,
    ];
}
