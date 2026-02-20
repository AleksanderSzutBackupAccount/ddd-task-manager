<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Project\Application\UseCases\Task\ChangeTaskStatus\ChangeTaskStatusCommand;
use Src\Project\Infrastructure\Laravel\Http\Requests\ChangeTaskStatusRequest;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class ChangeTaskStatusController
{
    public function __construct(
        private CommandHandlerInterface $commandBus
    ) {}

    public function __invoke(ChangeTaskStatusRequest $request, string $taskId): JsonResponse
    {

        $this->commandBus->handle(new ChangeTaskStatusCommand(
            taskId: $taskId,
            newStatus: $request->status
        ));

        return new JsonResponse(['message' => 'Status zadania został zmieniony']);
    }
}
