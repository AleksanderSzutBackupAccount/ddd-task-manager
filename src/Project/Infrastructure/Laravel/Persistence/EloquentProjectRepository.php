<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Collections\ProjectCollection;
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
        $model->id = $project->id();
        $model->name = $project->name();
        $model->slug = $project->slug();
        $model->save();

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

    public function getAssignedToUser(UserId $userId): ProjectCollection
    {
        /** @var Project[] $projects */
        $projects = ProjectModel::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $userId->value()))
            ->get()->map(fn (ProjectModel $project) => $project->toEntity())->toArray();

        return new ProjectCollection($projects);
    }
}
