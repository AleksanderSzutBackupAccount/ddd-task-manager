<?php

declare(strict_types=1);

use Src\Identity\Infrastructure\Laravel\Providers\IdentityServiceProvider;
use Src\Project\Infrastructure\Laravel\Providers\ProjectServiceProvider;
use Src\Shared\Infrastructure\Laravel\Providers\SharedServiceProvider;

return [
    SharedServiceProvider::class,
    IdentityServiceProvider::class,
    ProjectServiceProvider::class,
];
