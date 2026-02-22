<?php

declare(strict_types=1);

namespace Src\Identity\Application\Ports;

use Src\Identity\Domain\ValueObjects\UserId;

interface TokenParserInterface
{
    /**
     * @param non-empty-string $token
     */
    public function parse(string $token): ?UserId;
}
