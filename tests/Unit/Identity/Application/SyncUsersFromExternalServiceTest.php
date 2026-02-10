<?php

declare(strict_types=1);

namespace Tests\Unit\Identity\Application;

use PHPUnit\Framework\TestCase;
use Src\Identity\Application\External\ExternalUser;
use Src\Identity\Application\Ports\ExternalUserProvider;
use Src\Identity\Application\Services\SyncUsersFromExternalService;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;

final class SyncUsersFromExternalServiceTest extends TestCase
{
    public function test_it_syncs_users_from_provider_to_repository(): void
    {
        $externalUser1 = new ExternalUser('1', 'johndoe', 'john@example.com', 'John Doe');
        $externalUser2 = new ExternalUser('2', 'janedoe', 'jane@example.com', 'Jane Doe');

        $provider = $this->createMock(ExternalUserProvider::class);
        $provider->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([$externalUser1, $externalUser2]);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->exactly(2))
            ->method('upsert')
            ->with($this->isInstanceOf(User::class));

        $service = new SyncUsersFromExternalService($provider, $repository);
        $count = $service->sync();

        $this->assertEquals(2, $count);
    }

    public function test_it_returns_zero_when_no_users_fetched(): void
    {
        $provider = $this->createMock(ExternalUserProvider::class);
        $provider->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([]);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->never())
            ->method('upsert');

        $service = new SyncUsersFromExternalService($provider, $repository);
        $count = $service->sync();

        $this->assertEquals(0, $count);
    }

    public function test_it_propagates_exception_from_provider(): void
    {
        $provider = $this->createMock(ExternalUserProvider::class);
        $provider->expects($this->once())
            ->method('fetchUsers')
            ->willThrowException(new \Exception('Provider error'));

        $repository = $this->createMock(UserRepository::class);

        $service = new SyncUsersFromExternalService($provider, $repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Provider error');

        $service->sync();
    }

    public function test_it_stops_and_propagates_exception_from_repository(): void
    {
        $externalUser1 = new ExternalUser('1', 'johndoe', 'john@example.com', 'John Doe');

        $provider = $this->createMock(ExternalUserProvider::class);
        $provider->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([$externalUser1]);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->once())
            ->method('upsert')
            ->willThrowException(new \Exception('Database error'));

        $service = new SyncUsersFromExternalService($provider, $repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $service->sync();
    }

    public function test_it_fails_when_external_user_has_invalid_email(): void
    {
        $externalUser1 = new ExternalUser('1', 'johndoe', 'invalid-email', 'John Doe');

        $provider = $this->createMock(ExternalUserProvider::class);
        $provider->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([$externalUser1]);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->never())
            ->method('upsert');

        $service = new SyncUsersFromExternalService($provider, $repository);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address invalid-email');

        $service->sync();
    }
}
