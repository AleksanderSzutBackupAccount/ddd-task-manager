<?php

namespace Feature\Identity\Infrastructure\Laravel\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Identity\Infrastructure\Laravel\Models\UserModel;
use Tests\Helpers\AuthTestTrait;
use Tests\TestCase;

final class GetUsersTest extends TestCase
{
    use AuthTestTrait, RefreshDatabase;

    public function test_it_returns_logged_in_user_info(): void
    {
        UserModel::factory()->count(5)->create();
        $response = $this->callAsAuthorized(UserModel::query()->firstOrFail()->id)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_it_returns_unauthorized_when_no_token_provided(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated']);
    }
}
