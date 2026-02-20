<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\Task\CreateTask\CreateTaskCommand;
use Src\Project\Infrastructure\Laravel\Http\Requests\CreateTaskRequest;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class CreateTaskController
{
    public function __construct(
        private CommandHandlerInterface $commandBus
    ) {}

    public function __invoke(CreateTaskRequest $request, string $projectSlug): JsonResponse
    {
        $this->commandBus->handle(new CreateTaskCommand(
            projectSlug: $projectSlug,
            name: $request->name,
            description: $request->description,
            assignedUserId: UserId::fromNullable($request->assigned_user_id)
        ));

        return new JsonResponse(['message' => 'Zadanie zostało utworzone'], 201);
    }
}
