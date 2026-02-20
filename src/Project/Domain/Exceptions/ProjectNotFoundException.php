<?php

declare(strict_types=1);

namespace Src\Project\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;

final class ProjectNotFoundException extends DomainError
{
    public function errorCode(): string
    {
        return 'project_not_found';
    }

    protected function errorMessage(): string
    {
        return 'Project not found';
    }
}
