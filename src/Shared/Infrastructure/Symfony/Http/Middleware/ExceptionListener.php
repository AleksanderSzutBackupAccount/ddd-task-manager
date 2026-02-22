<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Http\Middleware;

use Src\Shared\Domain\Exceptions\DomainError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException && null !== $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        if ($exception instanceof DomainError) {
            $statusCode = 400;
            if (str_contains(get_class($exception), 'NotFound')) {
                $statusCode = 404;
            }

            $event->setResponse(new JsonResponse([
                'error' => $exception->errorCode(),
                'message' => $exception->getMessage(),
            ], $statusCode));

            return;
        }
    }
}
