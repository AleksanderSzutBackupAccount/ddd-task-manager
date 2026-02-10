<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Identity\Application\UseCases\Me\MeQuery;
use Src\Identity\Domain\User;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class MeController
{
    public function __construct(private QueryBusInterface $queryBus) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var string $userId */
        $userId = $request->attributes->get('auth_user_id');

        /** @var User $user */
        $user = $this->queryBus->ask(new MeQuery($userId));

        return new JsonResponse([
            'id' => $user->id->value,
            'name' => $user->name->value,
            'email' => $user->email->value,
        ]);
    }
}
