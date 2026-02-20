<?php

declare(strict_types=1);

namespace Src\Shared\Application\Auth;

interface TokenParserInterface
{
    /**
     * @param non-empty-string $token
     */
    public function parse(string $token): ?TokenPayload;
}
