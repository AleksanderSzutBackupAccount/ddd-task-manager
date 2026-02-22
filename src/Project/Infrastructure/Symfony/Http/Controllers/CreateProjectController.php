<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\CreateProject\CreateProjectCommand;
use Src\Shared\Domain\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class CreateProjectController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/api/projects', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('auth_user_id');
        /** @var array{name?: string, slug?: string, userIds?: string[]} $data */
        $data = json_decode($request->getContent(), true) ?? [];

        // Uproszczona walidacja dla przykładu
        if (empty($data['name']) || empty($data['slug'])) {
            return new JsonResponse(['message' => 'Invalid data'], 422);
        }

        try {
            $this->commandBus->dispatch(new CreateProjectCommand(
                $data['name'],
                $data['slug'],
                new UserId((string) $userId),
                $data['userIds'] ?? []
            ));
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        return new JsonResponse(null, 201);
    }
}
