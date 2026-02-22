<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Http\Middleware;

use Src\Identity\Application\Ports\TokenParserInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class AuthListener
{
    public function __construct(private TokenParserInterface $tokenParser)
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Pominiecie dla login
        if ('/api/auth/login' === $path) {
            return;
        }

        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            // Jeśli trasa wymaga autoryzacji (w tym projekcie prawie wszystkie)
            // W Symfony lepiej by było użyć Security, ale tutaj robimy uproszczony listener
            if (str_starts_with($path, '/api/')) {
                $event->setResponse(new JsonResponse(['message' => 'Unauthenticated'], 401));
            }

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
