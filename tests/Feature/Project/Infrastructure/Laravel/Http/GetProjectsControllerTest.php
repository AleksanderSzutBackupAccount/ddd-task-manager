<?php

declare(strict_types=1);

namespace Feature\Project\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\AuthTestTrait;
use Tests\TestCase;

final class GetProjectsControllerTest extends TestCase
{
    use AuthTestTrait, RefreshDatabase, WithFaker;

    const ENDPOINT = '/api/projects';

    public function test_create_success(): void
    {
        $token = $this->getUserJwtToken();

        $name = $this->faker->name();
        $slug = $this->faker->unique()->lexify('????');

        $response = $this->callAsAuthorized()->getJson(self::ENDPOINT);

        $response->assertStatus(200);

        $token = $response->isEmpty();

    }

    public function test_it_returns_unauthorized_when_no_token_provided(): void
    {
        $response = $this->getJson(self::ENDPOINT);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }
}
