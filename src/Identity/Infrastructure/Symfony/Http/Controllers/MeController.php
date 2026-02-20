<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Application\UseCases\Me\MeQuery;
use Src\Identity\Domain\User;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class MeController extends AbstractController
{
    public function __construct(private readonly QueryBusInterface $queryBus)
    {
    }

    #[Route('/api/auth/me', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('auth_user_id');

        if (!$userId) {
            return new JsonResponse(['message' => 'Unauthenticated'], 401);
        }

        /** @var User $user */
        $user = $this->queryBus->ask(new MeQuery((string) $userId));

        return new JsonResponse([
            'id' => $user->id->value,
            'name' => $user->name->value,
            'email' => $user->email->value,
        ]);
    }
}
