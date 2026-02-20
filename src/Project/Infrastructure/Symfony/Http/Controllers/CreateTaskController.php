<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Http\Controllers;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\Task\CreateTask\CreateTaskCommand;
use Src\Shared\Domain\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class CreateTaskController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/api/projects/{projectSlug}/tasks', methods: ['POST'])]
    public function __invoke(Request $request, string $projectSlug): JsonResponse
    {
        /** @var array{name?: string, description?: string, assigned_user_id?: string} $data */
        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['name']) || empty($data['description'])) {
            return new JsonResponse(['message' => 'Invalid data'], 422);
        }

        try {
            $this->commandBus->dispatch(new CreateTaskCommand(
                projectSlug: $projectSlug,
                name: $data['name'],
                description: $data['description'],
                assignedUserId: isset($data['assigned_user_id']) ? new UserId((string) $data['assigned_user_id']) : null
            ));
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Src\Project\Domain\Exceptions\ProjectNotFoundException) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Zadanie zostało utworzone'], 201);
    }
}
