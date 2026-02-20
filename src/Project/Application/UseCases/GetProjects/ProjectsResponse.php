<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetProjects;

use Src\Project\Domain\Collections\ProjectCollection;
use Src\Project\Domain\Project;

final readonly class ProjectsResponse
{
    public function __construct(private ProjectCollection $projects) {}

    /**
     * @return array<mixed>[]
     */
    public function toResponse(): array
    {
        return $this->projects->map(static fn (Project $project) => [
            'id' => $project->id(),
            'name' => $project->name(),
            'slug' => $project->slug(),
            'users' => $project->userIds(),
        ]);
    }
}
