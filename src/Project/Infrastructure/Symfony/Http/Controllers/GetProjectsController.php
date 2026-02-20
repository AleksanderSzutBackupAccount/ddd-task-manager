<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\GetProjects\GetProjectsQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetProjectsController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/api/projects', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('auth_user_id');

        $projects = $this->queryBus->ask(new GetProjectsQuery(
            new UserId((string) $userId),
        ));

        return new JsonResponse($projects->toResponse());
    }
}
