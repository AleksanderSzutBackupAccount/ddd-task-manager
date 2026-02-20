<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Bus;

use Closure;
use Illuminate\Contracts\Bus\Dispatcher;
use Src\Shared\Application\Bus\Query\Middleware\QueryMiddleware;
use Src\Shared\Application\Bus\Query\QueryBusInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

final readonly class QueryBus implements QueryBusInterface
{
    public function __construct(
        private Dispatcher $dispatcher,
        /**
         * @var list<QueryMiddleware>
         */
        private array $middleware = []
    ) {}

    /**
     * @template R
     *
     * @param  QueryInterface<R>  $query
     * @return R
     */
    public function ask(QueryInterface $query): mixed
    {

        $core = fn (QueryInterface $q): mixed => $this->dispatcher->dispatch($q);

        /**
         * @param  Closure(QueryInterface):mixed  $next
         * @param  QueryMiddleware  $middleware
         * @return Closure(QueryInterface):mixed
         */
        $reducer =
            static fn (Closure $next, QueryMiddleware $middleware): Closure => static fn (QueryInterface $query) => $middleware($query, $next);

        /** @var Closure(QueryInterface<R>):R $pipeline */
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            $reducer,
            $core
        );

        /** @var R */
        return $pipeline($query);
    }
}
