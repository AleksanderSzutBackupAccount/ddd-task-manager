<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Src\Identity\Application\Ports\TokenParserInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthMiddleware
{
    public function __construct(private TokenParserInterface $tokenParser) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token === null || $token === '') {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $this->tokenParser->parse($token);

        if ($userId === null) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->attributes->set('auth_user_id', $userId->value);

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
