<?php

declare(strict_types=1);

namespace Src\Project\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;

final class TaskNotFoundException extends DomainError
{
    public function errorCode(): string
    {
        return 'task_not_found';
    }

    protected function errorMessage(): string
    {
        return 'Task not found';
    }
}
