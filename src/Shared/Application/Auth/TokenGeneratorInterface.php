<?php

declare(strict_types=1);

namespace Src\Shared\Application\Auth;

interface TokenGeneratorInterface
{
    /**
     * @param array<string, mixed> $claims
     */
    public function generate(string $subject, array $claims = [], ?string $ttl = null): string;
}
