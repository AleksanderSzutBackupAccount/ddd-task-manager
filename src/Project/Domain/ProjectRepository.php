<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;

interface ProjectRepository
{
    public function save(Project $project): void;

    public function findById(ProjectId $id): ?Project;

    public function findBySlug(ProjectSlug $slug): ?Project;
}
