<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class LoginController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        /** @var array{email?: string} $data */
        $data = json_decode($request->getContent(), true) ?? [];

        if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Invalid email'], 422);
        }

        try {
            /** @var string $token */
            $token = $this->queryBus->ask(
                new LoginByEmailQuery((string) $data['email'])
            );
        } catch (\Src\Identity\Domain\Exceptions\UserNotFoundException) {
            return new JsonResponse(['error' => 'user_not_found'], 404);
        }

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}
