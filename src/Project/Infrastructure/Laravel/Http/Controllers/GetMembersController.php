<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Project\Application\UseCases\GetMembers\GetMembersQuery;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class GetMembersController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ProjectRepository $projectRepository
    ) {}

    public function __invoke(string $projectSlug, Request $request): JsonResponse
    {
        $project = $this->projectRepository->findBySlug(new ProjectSlug($projectSlug));

        if (! $project) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        }

        $members = $this->queryBus->ask(new GetMembersQuery(
            $project->id(),
        ));

        return new JsonResponse($members->toResponse());
    }
}
