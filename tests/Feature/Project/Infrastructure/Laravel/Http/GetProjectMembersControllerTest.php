<?php

declare(strict_types=1);

namespace Feature\Project\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;
use Tests\Helpers\AuthTestTrait;
use Tests\TestCase;

final class GetProjectMembersControllerTest extends TestCase
{
    use AuthTestTrait, RefreshDatabase, WithFaker;

    const ENDPOINT = '/api/projects/%s/members';

    public function test_create_success(): void
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->lexify('????');

        $response = $this->callAsAuthorized()->getJson($this->getProjectEndpoint());

        $response->assertStatus(200);

        $token = $response->isEmpty();

    }

    public function test_it_returns_unauthorized_when_no_token_provided(): void
    {
        $response = $this->getJson($this->getProjectEndpoint());

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }

    private function getProjectEndpoint(): string
    {
        $project = ProjectModel::query()->create([
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'slug' => $this->faker->unique()->lexify('????'),
        ]);

        return sprintf(self::ENDPOINT, $project->id);
    }
}
