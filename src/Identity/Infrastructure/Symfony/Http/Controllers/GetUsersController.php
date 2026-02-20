<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Application\UseCases\GetUsers\GetUsersQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetUsersController extends AbstractController
{
    public function __construct(private readonly QueryBusInterface $queryBus)
    {
    }

    #[Route('/api/users', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $users = $this->queryBus->ask(query: new GetUsersQuery());

        return new JsonResponse($users->toResponse());
    }
}
