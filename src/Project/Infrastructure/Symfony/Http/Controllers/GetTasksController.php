<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Http\Controllers;

use Src\Project\Application\UseCases\Task\GetAllTasks\GetAllTasksQuery;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetTasksController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly ProjectRepository $projectRepository,
    ) {
    }

    #[Route('/api/projects/{projectSlug}/tasks', methods: ['GET'])]
    public function __invoke(string $projectSlug): JsonResponse
    {
        try {
            $project = $this->projectRepository->findBySlug(new ProjectSlug($projectSlug));
        } catch (\InvalidArgumentException) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        }

        if (!$project) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        }

        $tasks = $this->queryBus->ask(new GetAllTasksQuery($project->id()));

        return new JsonResponse($tasks->toResponse());
    }
}
