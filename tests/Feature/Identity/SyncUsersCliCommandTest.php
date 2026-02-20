<?php

declare(strict_types=1);

namespace Tests\Feature\Identity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Identity\Application\External\ExternalUser;
use Src\Identity\Application\Ports\ExternalUserProvider;
use Tests\TestCase;

final class SyncUsersCliCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_users_via_cli_command(): void
    {
        $externalUser = new ExternalUser(
            externalId: '1',
            username: 'johndoe',
            email: 'john@example.com',
            name: 'John Doe'
        );

        $providerMock = $this->createMock(ExternalUserProvider::class);
        $providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([$externalUser]);

        $this->app->instance(ExternalUserProvider::class, $providerMock);

        $this->artisan('identity:sync-users')
            ->expectsOutput('Synced 1 users.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'external_id' => '1',
        ]);
    }

    public function test_it_handles_empty_sync(): void
    {
        $providerMock = $this->createMock(ExternalUserProvider::class);
        $providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([]);

        $this->app->instance(ExternalUserProvider::class, $providerMock);

        $this->artisan('identity:sync-users')
            ->expectsOutput('Synced 0 users.')
            ->assertExitCode(0);
    }

    public function test_it_fails_when_provider_throws_exception(): void
    {
        $providerMock = $this->createMock(ExternalUserProvider::class);
        $providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willThrowException(new \Exception('External service failure'));

        $this->app->instance(ExternalUserProvider::class, $providerMock);

        $this->artisan('identity:sync-users')
            ->expectsOutput('External service failure')
            ->assertExitCode(1);
    }
}
