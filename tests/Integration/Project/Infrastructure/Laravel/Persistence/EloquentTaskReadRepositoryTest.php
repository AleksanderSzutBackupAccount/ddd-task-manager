<?php

declare(strict_types=1);

namespace Tests\Integration\Project\Infrastructure\Laravel\Persistence;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;
use Src\Project\Infrastructure\Laravel\Persistence\EloquentTaskReadRepository;
use Tests\TestCase;

final class EloquentTaskReadRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private EloquentTaskReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(EloquentTaskReadRepository::class);
    }

    public function test_it_should_count_tasks_for_project(): void
    {
        $projectId = (string) ProjectId::generate();
        ProjectModel::query()->create([
            'id' => $projectId,
            'name' => 'Project 1',
            'slug' => 'PRJ1',
        ]);

        TaskModel::query()->create([
            'id' => $this->faker->uuid(),
            'slug' => 'PRJ1-1',
            'project_id' => $projectId,
            'name' => 'Task 1',
            'description' => 'Desc 1',
            'status' => TaskStatus::TO_DO,
        ]);

        TaskModel::query()->create([
            'id' => $this->faker->uuid(),
            'slug' => 'PRJ1-2',
            'project_id' => $projectId,
            'name' => 'Task 2',
            'description' => 'Desc 2',
            'status' => TaskStatus::TO_DO,
        ]);

        $count = $this->repository->countByProject(new ProjectId($projectId));

        $this->assertSame(2, $count);
    }

    public function test_it_should_find_all_tasks_for_project(): void
    {
        $projectId = (string) ProjectId::generate();
        ProjectModel::query()->create([
            'id' => $projectId,
            'name' => 'Project 1',
            'slug' => 'PRJ1',
        ]);

        TaskModel::query()->create([
            'id' => $this->faker->uuid(),
            'slug' => 'PRJ1-1',
            'project_id' => $projectId,
            'name' => 'Task 1',
            'description' => 'Desc 1',
            'status' => TaskStatus::TO_DO,
        ]);

        $tasks = $this->repository->findAll(new ProjectId($projectId));

        $this->assertCount(1, $tasks);
        $this->assertSame('PRJ1-1', $tasks->items()[0]->slug()->value());
    }
}
