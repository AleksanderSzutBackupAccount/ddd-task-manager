<?php

declare(strict_types=1);

namespace Tests\Feature\Project\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Helpers\AuthTestTrait;
use Tests\TestCase;

final class CreateProjectControllerTest extends TestCase
{
    use AuthTestTrait, RefreshDatabase, WithFaker;

    const ENDPOINT = '/api/projects';

    public function test_create_success(): void
    {
        $token = $this->getUserJwtToken();

        $name = $this->faker->name();
        $slug = $this->faker->unique()->lexify('????');

        $response = $this->callAsAuthorized()->postJson(self::ENDPOINT, [
            'name' => $name,
            'slug' => $slug,
        ]);

        $response->assertStatus(200);

        $token = $response->isEmpty();

    }

    public function test_it_returns_unauthorized_when_no_token_provided(): void
    {
        $response = $this->postJson(self::ENDPOINT);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }

    public function test_create_validation_errors(): void
    {
        $token = $this->getUserJwtToken();

        $response = $this->callAsAuthorized()->postJson(self::ENDPOINT, [
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonValidationErrors(['slug']);
    }

    #[DataProvider('slugValidationProvider')]
    public function test_slug_validation(string $slug, string $expectedError): void
    {
        $this->callAsAuthorized()->postJson(self::ENDPOINT, [
            'name' => 'Existing Project',
            'slug' => 'exis',
        ])->assertStatus(200);

        $response = $this->callAsAuthorized()->postJson(self::ENDPOINT, [
            'name' => 'New Project',
            'slug' => $slug,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public static function slugValidationProvider(): array
    {
        return [
            'slug too long' => ['tolong', 'max'],
            'slug not unique' => ['exis', 'unique'],
        ];
    }
}
