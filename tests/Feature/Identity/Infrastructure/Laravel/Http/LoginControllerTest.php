<?php

namespace Tests\Feature\Identity\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Tests\TestCase;

final class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_for_existing_user(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/auth/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        $token = $response->json('token');
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function test_login_returns_error_for_non_existent_user(): void
    {
        $response = $this->postJson('/auth/login', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'user_not_found',
                'message' => 'User not found',
            ]);
    }

    public function test_login_validation_errors(): void
    {
        // Missing email
        $response = $this->postJson('/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Invalid email format
        $response = $this->postJson('/auth/login', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
