<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Providers;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Src\Shared\Application\Auth\TokenGeneratorInterface;
use Src\Shared\Application\Auth\TokenParserInterface;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Src\Shared\Application\Log\LoggerInterface;
use Src\Shared\Domain\Bus\EventBusInterface;
use Src\Shared\Infrastructure\Auth\Lcobucci\JwtTokenGenerator;
use Src\Shared\Infrastructure\Auth\Lcobucci\JwtTokenParser;
use Src\Shared\Infrastructure\Auth\Lcobucci\LcobucciConfigProvider;
use Src\Shared\Infrastructure\Laravel\Bus\CommandHandler;
use Src\Shared\Infrastructure\Laravel\Bus\EventBusLaravel;
use Src\Shared\Infrastructure\Laravel\Bus\Query\Middleware\CacheMiddleware;
use Src\Shared\Infrastructure\Laravel\Bus\QueryBus;
use Src\Shared\Infrastructure\Laravel\Log\LaravelLogger;

final class SharedServiceProvider extends BaseContextServiceProvider
{
    protected array $binds = [
        EventBusInterface::class => EventBusLaravel::class,
        CommandHandlerInterface::class => CommandHandler::class,
        LoggerInterface::class => LaravelLogger::class,
        TokenGeneratorInterface::class => JwtTokenGenerator::class,
        TokenParserInterface::class => JwtTokenParser::class,
    ];

    public function register(): void
    {
        $this->app->singleton(LcobucciConfigProvider::class, function () {
            /** @var non-empty-string $appKey */
            $appKey = config('app.key');

            return new LcobucciConfigProvider($appKey);
        });
        $this->app->singleton(QueryBusInterface::class, function ($app) {
            /** @var Application $app */
            return new QueryBus(
                $app->make(Dispatcher::class),
                middleware: [
                    $app->make(CacheMiddleware::class),
                ]
            );
        });

        parent::register();
    }

    protected array $providers = [EventSharedServiceProvider::class];
}
