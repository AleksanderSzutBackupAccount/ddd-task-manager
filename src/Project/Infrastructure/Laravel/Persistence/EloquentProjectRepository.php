<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;

final class EloquentProjectRepository implements ProjectRepository
{
    public function save(Project $project): void
    {
        /** @var ProjectModel $model */
        $model = ProjectModel::query()->find((string) $project->id()) ?? new ProjectModel;
        $model->id = (string) $project->id();
        $model->name = $project->name();
        $model->slug = (string) $project->slug();
        $model->save();

        // sync users
        $model->users()->sync($project->userIds());
    }

    public function findById(ProjectId $id): ?Project
    {
        /** @var ProjectModel|null $model */
        $model = ProjectModel::query()->find((string) $id);

        return $model?->toEntity();
    }

    public function findBySlug(ProjectSlug $slug): ?Project
    {
        /** @var ProjectModel|null $model */
        $model = ProjectModel::query()->where('slug', (string) $slug)->first();

        return $model?->toEntity();
    }
}
