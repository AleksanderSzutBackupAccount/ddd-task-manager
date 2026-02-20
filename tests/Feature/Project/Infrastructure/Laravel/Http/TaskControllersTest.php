<?php

declare(strict_types=1);

namespace Feature\Project\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;
use Src\Project\Infrastructure\Laravel\Models\TaskModel;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Tests\Helpers\AuthTestTrait;
use Tests\TestCase;

final class TaskControllersTest extends TestCase
{
    use AuthTestTrait, RefreshDatabase, WithFaker;

    public function test_it_should_get_tasks_for_project(): void
    {
        $project = ProjectModel::query()->create([
            'id' => $this->faker->uuid(),
            'name' => 'Project 1',
            'slug' => 'PRJ1',
        ]);

        TaskModel::query()->create([
            'id' => $this->faker->uuid(),
            'slug' => 'PRJ1-1',
            'project_id' => $project->id,
            'name' => 'Task 1',
            'description' => 'Desc 1',
            'status' => TaskStatus::TO_DO,
        ]);

        $response = $this->callAsAuthorized()->getJson("/api/projects/PRJ1/tasks");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['slug' => 'PRJ1-1', 'name' => 'Task 1']);
    }

    public function test_it_should_change_task_status(): void
    {
        $project = ProjectModel::query()->create([
            'id' => $this->faker->uuid(),
            'name' => 'Project 1',
            'slug' => 'PRJ1',
        ]);

        $taskId = $this->faker->uuid();

        // Emulujemy zdarzenie w EventStore, bo repozytorium zapisu jest zdarzeniowe
        /** @var \Src\Shared\Domain\EventStore\EventStore $eventStore */
        $eventStore = $this->app->make(\Src\Shared\Domain\EventStore\EventStore::class);
        $event = new \Src\Project\Domain\Events\TaskCreated(
            $taskId,
            (string) $project->id,
            'PRJ1-1',
            'Task 1',
            'Desc 1',
            TaskStatus::TO_DO,
            null
        );
        $eventStore->append([$event]);

        // Musimy też ręcznie uruchomić projekcję, aby TaskModel istniał w bazie do odczytu
        /** @var \Src\Shared\Domain\Bus\EventProjector $projector */
        $projector = $this->app->make(\Src\Shared\Domain\Bus\EventProjector::class);
        $projector->project($event);

        $response = $this->callAsAuthorized()->patchJson("/api/tasks/{$taskId}/status", [
            'status' => TaskStatus::IN_PROGRESS,
        ]);

        $response->assertStatus(200);

        // Odświeżamy tabelę zadania, bo projekcja mogła zostać uruchomiona przez save
        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            'status' => TaskStatus::IN_PROGRESS,
        ]);
    }

    public function test_it_should_return_404_when_project_not_found(): void
    {
        $response = $this->callAsAuthorized()->getJson("/api/projects/NONE/tasks");
        $response->assertStatus(404);
    }
}
