<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\GetProjects\GetProjectsQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class GetProjectsController
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var non-empty-string $userId */
        $userId = $request->attributes->get('auth_user_id');

        $projects = $this->queryBus->ask(new GetProjectsQuery(
            new UserId($userId),
        ));

        return new JsonResponse($projects->toResponse());
    }
}
