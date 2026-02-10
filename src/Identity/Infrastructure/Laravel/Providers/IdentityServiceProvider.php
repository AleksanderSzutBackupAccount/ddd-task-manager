<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Providers;

use Src\Identity\Application\Ports\ExternalUserProvider;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQuery;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQueryHandler;
use Src\Identity\Application\UseCases\Me\MeQuery;
use Src\Identity\Application\UseCases\Me\MeQueryHandler;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Infrastructure\JsonPlaceholder\UserJsonPlaceholderProvider;
use Src\Identity\Infrastructure\Laravel\Console\SyncUsersCliCommand;
use Src\Identity\Infrastructure\Laravel\Persistence\UserLaravelRepository;
use Src\Shared\Infrastructure\Laravel\Providers\BaseContextServiceProvider;

final class IdentityServiceProvider extends BaseContextServiceProvider
{
    protected array $binds = [
        UserRepository::class => UserLaravelRepository::class,
        ExternalUserProvider::class => UserJsonPlaceholderProvider::class,
    ];

    protected array $useCases = [
        LoginByEmailQuery::class => LoginByEmailQueryHandler::class,
        MeQuery::class => MeQueryHandler::class,
    ];

    protected array $commands = [
        SyncUsersCliCommand::class,
    ];

    public function register(): void
    {
        parent::register();
    }
}
