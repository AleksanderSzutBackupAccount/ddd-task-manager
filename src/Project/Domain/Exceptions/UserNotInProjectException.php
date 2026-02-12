<?php

declare(strict_types=1);

namespace Src\Project\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;

final class UserNotInProjectException extends DomainError
{
    public function errorCode(): string
    {
        return 'user_not_in_project';
    }

    protected function errorMessage(): string
    {
        return 'User is not assigned to the project';
    }
}
