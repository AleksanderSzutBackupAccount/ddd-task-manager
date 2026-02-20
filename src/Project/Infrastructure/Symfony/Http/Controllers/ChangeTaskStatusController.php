<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Http\Controllers;

use Src\Project\Application\UseCases\Task\ChangeTaskStatus\ChangeTaskStatusCommand;
use Src\Shared\Domain\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ChangeTaskStatusController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/api/tasks/{taskId}/status', methods: ['PATCH'])]
    public function __invoke(Request $request, string $taskId): JsonResponse
    {
        /** @var array{status?: string} $data */
        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['status'])) {
            return new JsonResponse(['message' => 'Invalid data'], 422);
        }

        try {
            $this->commandBus->dispatch(new ChangeTaskStatusCommand(
                taskId: $taskId,
                newStatus: (string) $data['status']
            ));
        } catch (\Src\Project\Domain\Exceptions\TaskNotFoundException) {
            return new JsonResponse(['message' => 'Task not found'], 404);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Status zadania został zmieniony']);
    }
}
