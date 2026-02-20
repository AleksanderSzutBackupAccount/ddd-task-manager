<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Application\UseCases\CreateProject\CreateProjectCommand;
use Src\Project\Infrastructure\Laravel\Http\Requests\CreateProjectRequest;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class CreateProjectController
{
    public function __construct(
        private CommandHandlerInterface $commandHandler
    ) {}

    public function __invoke(CreateProjectRequest $request): JsonResponse
    {
        /** @var non-empty-string $userId */
        $userId = $request->attributes->get('auth_user_id');

        $this->commandHandler->handle(new CreateProjectCommand(
            $request->name,
            $request->slug,
            new UserId($userId)
        ));

        return new JsonResponse;
    }
}
