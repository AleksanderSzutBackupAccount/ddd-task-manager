<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Providers;

use Src\Identity\Application\Ports\ExternalUserProvider;
use Src\Identity\Application\Services\SyncUsersFromExternalService;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Infrastructure\JsonPlaceholder\UserJsonPlaceholderProvider;
use Src\Identity\Infrastructure\Laravel\Console\SyncUsersCliCommand;
use Src\Identity\Infrastructure\Laravel\Persistence\UserLaravelRepository;
use Src\Shared\Infrastructure\Laravel\Providers\BaseContextServiceProvider;

final  class IdentityServiceProvider extends BaseContextServiceProvider
{
    protected array $binds = [
        UserRepository::class => UserLaravelRepository::class,
        ExternalUserProvider::class => UserJsonPlaceholderProvider::class,
    ];

    protected array $commands = [
        SyncUsersCliCommand::class
    ];
}
