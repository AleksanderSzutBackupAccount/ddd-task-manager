<?php

declare(strict_types=1);

namespace Src\Identity\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;

final class UserNotFoundException extends DomainError
{
    public function errorCode(): string
    {
        return 'user_not_found';
    }

    protected function errorMessage(): string
    {
        return 'User not found';
    }
}
