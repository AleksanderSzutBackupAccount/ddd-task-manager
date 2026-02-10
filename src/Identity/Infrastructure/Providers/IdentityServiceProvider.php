<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Providers;

use Src\Identity\Application\Ports\ExternalUserProvider;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Application\Ports\TokenParserInterface;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQuery;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQueryHandler;
use Src\Identity\Application\UseCases\Me\MeQuery;
use Src\Identity\Application\UseCases\Me\MeQueryHandler;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Infrastructure\JsonPlaceholder\UserJsonPlaceholderProvider;
use Src\Identity\Infrastructure\Laravel\Console\SyncUsersCliCommand;
use Src\Identity\Infrastructure\Laravel\Persistence\UserLaravelRepository;
use Src\Identity\Infrastructure\Lcobucci\JwtTokenGenerator;
use Src\Identity\Infrastructure\Lcobucci\JwtTokenParser;
use Src\Identity\Infrastructure\Lcobucci\LcobucciConfigProvider;
use Src\Shared\Infrastructure\Laravel\Providers\BaseContextServiceProvider;

final class IdentityServiceProvider extends BaseContextServiceProvider
{
    protected array $binds = [
        UserRepository::class => UserLaravelRepository::class,
        ExternalUserProvider::class => UserJsonPlaceholderProvider::class,
        TokenGeneratorInterface::class => JwtTokenGenerator::class,
        TokenParserInterface::class => JwtTokenParser::class,
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
        /** @var non-empty-string $appKey */
        $appKey = config('app.key');
        $this->app->bind(LcobucciConfigProvider::class, fn () => LcobucciConfigProvider::fromDecoded($appKey));

        parent::register();
    }
}
