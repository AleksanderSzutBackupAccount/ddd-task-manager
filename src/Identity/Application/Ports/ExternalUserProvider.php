<?php

declare(strict_types=1);


namespace Src\Identity\Application\Ports;

use Src\Identity\Application\External\ExternalUser;

interface ExternalUserProvider
{
    /**
     * @return ExternalUser[]
     */
    public function fetchUsers(): array;
}
