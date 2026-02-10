<?php

declare(strict_types=1);

namespace Src\Identity\Application\Services;

use Src\Identity\Application\Ports\ExternalUserProvider;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserName;
use Src\Shared\Domain\Exceptions\InvalidValueObjectException;

final readonly class SyncUsersFromExternalService
{
    public function __construct(
        private ExternalUserProvider $provider,
        private UserRepository $users,
    ) {}

    /**
     * @return int number of synced users
     *
     * @throws InvalidValueObjectException
     */
    public function sync(): int
    {
        $externalUsers = $this->provider->fetchUsers();

        foreach ($externalUsers as $external) {
            $user = User::import(
                name: new Username($external->username),
                email: UserEmail::fromString($external->email),
                externalId: new UserExternalId($external->externalId),
            );

            $this->users->upsert($user);
        }

        return count($externalUsers);
    }
}
