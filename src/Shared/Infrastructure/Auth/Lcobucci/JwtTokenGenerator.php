<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Auth\Lcobucci;

use Src\Shared\Application\Auth\TokenGeneratorInterface;

final readonly class JwtTokenGenerator implements TokenGeneratorInterface
{
    public function __construct(private LcobucciConfigProvider $configProvider)
    {
    }

    /**
     * @param non-empty-string                          $subject
     * @param array<non-empty-string, non-empty-string> $claims
     * @param non-empty-string|null                     $ttl
     */
    public function generate(string $subject, array $claims = [], ?string $ttl = null): string
    {
        $now = new \DateTimeImmutable();
        $ttl = $ttl ?? LcobucciConfigProvider::DEFAULT_TTL;

        $builder = $this->configProvider->config->builder()
            ->issuedBy(LcobucciConfigProvider::ISSUER)
            ->permittedFor(LcobucciConfigProvider::PERMITTED_FOR)
            ->relatedTo($subject)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify($ttl));

        foreach ($claims as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        return $builder
            ->getToken($this->configProvider->config->signer(), $this->configProvider->config->signingKey())
            ->toString();
    }
}
