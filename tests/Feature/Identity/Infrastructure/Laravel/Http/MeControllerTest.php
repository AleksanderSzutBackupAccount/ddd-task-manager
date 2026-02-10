<?php

namespace Tests\Feature\Identity\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Tests\TestCase;

final class MeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_logged_in_user_info(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create([
            'email' => 'auth-test@example.com',
            'name' => 'Auth Test User',
        ]);

        $loginResponse = $this->postJson('/auth/login', [
            'email' => 'auth-test@example.com',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->withToken($token)
            ->getJson('/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => (string) $user->id,
                'name' => 'Auth Test User',
                'email' => 'auth-test@example.com',
            ]);
    }

    public function test_it_returns_unauthorized_when_no_token_provided(): void
    {
        $response = $this->getJson('/auth/me');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }

    public function test_it_returns_unauthorized_when_invalid_token_provided(): void
    {
        $response = $this->withToken('invalid-token')
            ->getJson('/auth/me');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }
}
