<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQuery;
use Src\Shared\Application\Bus\Query\QueryBusInterface;

final readonly class LoginController
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        /** @var string $token */
        $token = $this->queryBus->ask(
            new LoginByEmailQuery((string)$request->input('email'))
        );

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}
