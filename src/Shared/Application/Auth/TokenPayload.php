<?php

declare(strict_types=1);

namespace Src\Shared\Application\Auth;

final readonly class TokenPayload
{
    /**
     * @param array<string, mixed> $claims
     */
    public function __construct(
        public string $subject,
        public array $claims,
    ) {
    }

    public function getClaim(string $name): mixed
    {
        return $this->claims[$name] ?? null;
    }
}
