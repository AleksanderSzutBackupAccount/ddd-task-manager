<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Src\Shared\Application\Auth\TokenParserInterface;
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

        $payload = $this->tokenParser->parse($token);

        if ($payload === null) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->attributes->set('auth_user_id', $payload->subject);

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
