<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\GetMembers\GetMembersQuery;
use Src\Project\Application\UseCases\GetProjects\GetProjectsQuery;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class GetMembersController
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {}

    public function __invoke(string $projectId, Request $request): JsonResponse
    {
        $members = $this->queryBus->ask(new GetMembersQuery(
            new ProjectId($projectId),
        ));

        return new JsonResponse($members->toResponse());
    }
}
