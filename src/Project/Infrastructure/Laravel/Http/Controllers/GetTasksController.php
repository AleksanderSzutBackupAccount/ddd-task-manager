<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Project\Application\UseCases\Task\GetAllTasks\GetAllTasksQuery;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class GetTasksController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ProjectRepository $projectRepository
    ) {}

    public function __invoke(string $projectSlug): JsonResponse
    {
        $project = $this->projectRepository->findBySlug(new ProjectSlug($projectSlug));

        if (! $project) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        }

        $tasks = $this->queryBus->ask(new GetAllTasksQuery($project->id()));

        return new JsonResponse($tasks->toResponse());
    }
}
