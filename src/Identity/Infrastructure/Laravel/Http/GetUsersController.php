<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Identity\Application\UseCases\GetUsers\GetUsersQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class GetUsersController
{
    public function __construct(private QueryBusInterface $queryBus) {}

    public function __invoke(Request $request): JsonResponse
    {
        $users = $this->queryBus->ask(query: new GetUsersQuery);

        return new JsonResponse($users->toResponse());
    }
}
