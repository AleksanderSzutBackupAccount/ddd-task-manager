<?php

declare(strict_types=1);

use Src\Identity\Infrastructure\Providers\IdentityServiceProvider;
use Src\Shared\Infrastructure\Laravel\Providers\SharedServiceProvider;

return [
    SharedServiceProvider::class,
    IdentityServiceProvider::class,
];
