<?php

declare(strict_types=1);

namespace Src\Identity\Application\Ports;

use Src\Identity\Domain\User;

interface TokenGeneratorInterface
{
    public function generate(User $user): string;
}
