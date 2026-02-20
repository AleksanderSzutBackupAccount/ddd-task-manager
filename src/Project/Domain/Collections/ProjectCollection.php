<?php

declare(strict_types=1);

namespace Src\Project\Domain\Collections;

use Src\Project\Domain\Project;
use Src\Shared\Domain\Collection\Collection;

/**
 * @extends Collection<Project>
 */
final class ProjectCollection extends Collection
{
    protected function type(): string
    {
        return Project::class;
    }
}
