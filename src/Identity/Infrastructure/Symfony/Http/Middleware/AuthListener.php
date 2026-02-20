<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Http\Middleware;

use Src\Identity\Application\Ports\TokenParserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class AuthListener
{
    public function __construct(private TokenParserInterface $tokenParser)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Only authenticate /api/auth/me and potentially other protected routes
        // For this task, we focus on /api/auth/me
        if ('/api/auth/me' !== $path) {
            return;
        }

        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $event->setResponse(new JsonResponse(['message' => 'Unauthenticated'], 401));

            return;
        }

        $token = substr($authHeader, 7);
        $userId = $this->tokenParser->parse($token);

        if (null === $userId) {
            $event->setResponse(new JsonResponse(['message' => 'Unauthenticated'], 401));

            return;
        }

        $request->attributes->set('auth_user_id', $userId->value);
    }
}
